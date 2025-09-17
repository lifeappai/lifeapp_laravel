<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\GameType;
use App\Enums\UserType;
use App\Enums\StatusEnum;
use App\Http\Resources\API\V3\LaMissionAssignResource;
use App\Http\Resources\API\V3\LaTeacherGradeResource;
use App\Http\Resources\PublicUserResrouce;
use App\Models\LaMission;
use App\Models\LaMissionAssign;
use App\Models\LaTeacherGrade;
use App\Models\LaTopicAssign;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Resources\API\V3\LaMissionResource;
use App\Models\LaMissionComplete;
use App\Http\Resources\API\V3\LaMissionCompleteResource;
use App\Models\CoinTransaction;
use Illuminate\Support\Facades\DB;
use App\Models\Vision;
use App\Models\VisionAssign;
use App\Models\VisionQuestionAnswer;
use App\Models\VisionUserStatus;
use App\Http\Resources\API\V3\LaVisionResource;
use App\Http\Resources\API\V3\LaVisionAssignResource;

class LaTeacherController extends ResponseController
{
    public function teacherGrades()
    {
        try {
            $teacherGrades = LaTeacherGrade::where('user_id', Auth::user()->id)->get();
            $response['teacherGrades'] = LaTeacherGradeResource::collection($teacherGrades);
            return $this->sendResponse($response, "Teacher Grades");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getStudents(Request $request)
    {
        try {
            $validate = array(
                'school_id' => ['required', 'exists:schools,id'],
                'la_section_id' => ['required', 'exists:la_sections,id'],
                'la_subject_id' => ['nullable', 'exists:la_subjects,id'],
                'la_grade_id' => ['required', 'exists:la_grades,id'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $users = User::where('school_id', $request->school_id)->where('la_grade_id', $request->la_grade_id)->where('la_section_id', $request->la_section_id)->where('type', UserType::Student)->paginate(30);

            $response['users'] =  PublicUserResrouce::collection($users)->response()->getData(true);
            return $this->sendResponse($response, "Class Users");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function assignMissions(Request $request)
    {
        try {
            $validate = [
                'la_mission_id' => ['required', 'exists:la_missions,id'],
                'user_ids' => ['required', 'array'],
                'due_date' => ['required'],
            ];
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $mission = LaMission::find($request->la_mission_id);
            $teacher = Auth::user();

            foreach ($request->user_ids as $userId) {
                LaMissionAssign::create([
                    "teacher_id"    => $teacher->id,
                    "user_id"       => $userId,
                    "la_mission_id" => $mission->id,
                    "due_date"      => date("Y-m-d", strtotime($request->due_date)),
                    "type"          => $mission->type,
                ]);
            }

            // ✅ Calculate coins earned for assigning
            $level = $mission->laLevel ?? null;
            $coinsToAward = ($level->teacher_assign_points ?? 0) * count($request->user_ids);

            if ($coinsToAward > 0) {
                $teacher->createTransaction(
                    $mission, 
                    $coinsToAward, 
                    CoinTransaction::TYPE_ASSIGN_TASK
                );
            }

            // ✅ Notify students
            $mission->sendAssignedMissionNotification($request->user_ids);

            // ✅ Return coins info in response
            return $this->sendResponse([
                "mission_id"       => $mission->id,
                "assigned_students"=> count($request->user_ids),
                "coins_per_student"=> $level->teacher_assign_points ?? 0,
                "total_coins_earned"=> $coinsToAward,
                "transaction_type" => CoinTransaction::TYPE_ASSIGN_TASK,
            ], "Mission Assigned Successfully");

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function assignTopics(Request $request)
    {
        try {
            $validate = array(
                'la_topic_id' => ['required', 'exists:la_topics,id'],
                'user_ids' => ['required', 'array'],
                'due_date' => ['required'],
                'type' => ['required', Rule::in([GameType::QUIZ, GameType::RIDDLE, GameType::PUZZLE])],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            foreach ($request->user_ids as $userId) {
                LaTopicAssign::create([
                    "teacher_id" => Auth::user()->id,
                    "user_id" => $userId,
                    "la_topic_id" => $request->la_topic_id,
                    "due_date" => date("Y-m-d", strtotime($request->due_date)),
                    "type" => $request->type,
                ]);
            }
            return $this->sendResponse("", "Topic Assigned");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getTeacherMissions(Request $request)
    {
        try {
            $query = LaMission::whereHas('laMissionAssigns', function($q){
                $q->where('teacher_id', Auth::id());
            });

            // Apply filters if provided
            if ($request->has('la_level_id')) {
                $query->where('la_level_id', $request->la_level_id);
            }

            if ($request->has('la_subject_id')) {
                $query->where('la_subject_id', $request->la_subject_id);
            }

            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            $missions = $query->paginate(250);

            $response['missions'] = LaMissionResource::collection($missions)
                ->response()->getData(true);

            return $this->sendResponse($response, "Teacher Missions");

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getMissionParticipants(Request $request, LaMission $laMission)
    {
        try{ 
            $laAssignMissions = LaMissionAssign::orderByDesc('created_at')->where('teacher_id', Auth::user()->id)
            ->where('la_mission_id', $laMission->id)
            ->paginate(15);

            $users =  LaMissionAssignResource::collection($laAssignMissions)->response()->getData(true);
            return $this->sendResponse($users, "Teacher Mission Participants");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function approveRejectUserMission(LaMissionComplete $laMissionComplete, Request $request)
    {
        try {
            $validate = [
                'comment' => 'required',
                'status'  => 'required|in:0,1', // 0 = rejected, 1 = approved
            ];
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $user    = $laMissionComplete->user;
            $mission = $laMissionComplete->laMission;
            $points  = $mission->getGamePoints() ?? 0;

            $message         = "Mission Rejected";
            $teacherCoins    = 0; // 🔹 track teacher reward

            if ((int) $request->status === 1) {
                // ✅ Student reward
                if ($points > 0) {
                    $laMissionComplete->points = $points;
                    $user->createTransaction($laMissionComplete, $points, CoinTransaction::TYPE_MISSION);
                }

                // update status + approved_at
                $laMissionComplete->status = 'completed';
                $laMissionComplete->save();
                $laMissionComplete->approved($request->comment);

                $mission->sendApproveNotification($user);
                $message = "Mission Approved";

                // ✅ Teacher reward per correct submission
                $assign = \App\Models\LaMissionAssign::where('la_mission_id', $mission->id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($assign && $assign->teacher_id) {
                    $teacher = \App\Models\User::find($assign->teacher_id);
                    $level   = $mission->laLevel ?? null;

                    $alreadyRewarded = \App\Models\CoinTransaction::where([
                        'user_id'        => $teacher->id,
                        'coinable_type'  => get_class($laMissionComplete),
                        'coinable_id'    => $laMissionComplete->id,
                        'type'           => CoinTransaction::TYPE_CORRECT_SUBMISSION,
                    ])->exists();

                    if (!$alreadyRewarded && $level && $level->teacher_correct_submission_points > 0) {
                        $teacher->createTransaction(
                            $laMissionComplete,
                            $level->teacher_correct_submission_points,
                            CoinTransaction::TYPE_CORRECT_SUBMISSION
                        );
                        $teacherCoins = $level->teacher_correct_submission_points; // 🔹 capture teacher reward
                    }
                }
            } else {
                // update status + rejected_at
                $laMissionComplete->status = 'rejected';
                $laMissionComplete->save();
                $laMissionComplete->rejected($request->comment);

                $mission->sendRejectNotification($user);
            }

            $response = [
                "submit_mission_id" => $laMissionComplete->id,
                "allocated_coins"   => $laMissionComplete->points ?? 0,   // student coins
                "teacher_coins"     => $teacherCoins,                    // 🔹 teacher coins
                "status"            => $laMissionComplete->status,
            ];
            return $this->sendResponse($response, $message);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function index(Request $request)
    {
        try {
            // Validation rules
            $validate = [
                'la_subject_id' => ['nullable', 'exists:la_subjects,id'],
                'la_level_id' => ['nullable', 'exists:la_levels,id'],
                'chapter_id' => ['nullable', 'exists:chapters,id'], // ✅ new filter
                'search_title' => ['nullable', 'string'],
                'search_lang' => ['nullable', 'string'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:10000'], 
                'page' => ['nullable', 'integer', 'min:1'],
            ];

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $user = Auth::user();
            if (!$user || $user->type !== UserType::Teacher) {
                return $this->sendError('Only teachers can access this endpoint.', 403);
            }

            $visions = Vision::where('status', StatusEnum::ACTIVE)
                ->whereIn('allow_for', [
                    GameType::ALLOW_FOR['BY_TEACHER'],
                    GameType::ALLOW_FOR['ALL'],
                ])
                ->orderBy('index');

            // ✅ Filters
            if ($request->filled('la_subject_id')) {
                $visions->where('la_subject_id', $request->la_subject_id);
            }

            if ($request->filled('la_level_id')) {
                $visions->where('la_level_id', $request->la_level_id);
            }

            if ($request->filled('chapter_id')) {
                $visions->where('chapter_id', $request->chapter_id); // ✅ filter by chapter
            }

            if ($request->filled('search_title')) {
                $searchTitle = strtolower($request->search_title);

                $visions->where(function ($query) use ($searchTitle) {
                    $query->whereRaw("JSON_SEARCH(LOWER(title), 'all', ?) IS NOT NULL", ["%$searchTitle%"])
                        ->orWhereRaw("JSON_SEARCH(LOWER(description), 'all', ?) IS NOT NULL", ["%$searchTitle%"]);
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $data = $visions->paginate($perPage);

            $response['visions'] = LaVisionResource::collection($data)->response()->getData(true);

            return $this->sendResponse($response, "Teacher visions list");

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function assignVision(Request $request)
    {
        try {
            $validate = [
                'vision_id' => ['required', 'exists:visions,id'],
                'user_ids'  => ['required', 'array'],
                'due_date'  => ['required', 'date'],
            ];

            $validator = Validator::make($request->all(), $validate);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $vision = Vision::find($request->vision_id);
            $teacherId = Auth::id();

            $alreadyAssignedIds = [];
            $newlyAssignedIds = [];

            foreach ($request->user_ids as $userId) {
                $exists = VisionAssign::where('vision_id', $request->vision_id)
                    ->where('teacher_id', $teacherId)
                    ->where('student_id', $userId)
                    ->exists();

                if ($exists) {
                    $alreadyAssignedIds[] = $userId;
                } else {
                    VisionAssign::create([
                        'teacher_id' => $teacherId,
                        'student_id' => $userId,
                        'vision_id'  => $request->vision_id,
                        'due_date'   => date('Y-m-d', strtotime($request->due_date)),
                    ]);
                    $newlyAssignedIds[] = $userId;
                }
            }

            if (count($newlyAssignedIds) > 0) {
                // ✅ Notify students
                $vision->sendAssignedVisionNotification($newlyAssignedIds);

                // ✅ Reward teacher per new assignment
                $teacher = Auth::user();
                $level = $vision->laLevel ?? null;

                $coinsToAward = ($level->teacher_assign_points ?? 0) * count($newlyAssignedIds);

                if ($coinsToAward > 0) {
                    $teacher->createTransaction($vision, $coinsToAward, CoinTransaction::TYPE_ASSIGN_TASK);
                }
            }

            // ✅ Build feedback message
            $alreadyAssignedNames = User::whereIn('id', $alreadyAssignedIds)->pluck('name')->toArray();
            $newlyAssignedNames = User::whereIn('id', $newlyAssignedIds)->pluck('name')->toArray();

            if (count($alreadyAssignedIds) === count($request->user_ids)) {
                return $this->sendResponse("", "Vision has already been assigned to all selected students.");
            }

            $message = "";

            if (!empty($alreadyAssignedNames)) {
                $message .= "Vision was already assigned to: " . implode(", ", $alreadyAssignedNames) . ". ";
            }

            if (!empty($newlyAssignedNames)) {
                $message .= "Vision successfully assigned to: " . implode(", ", $newlyAssignedNames) . ".";
            }

            return $this->sendResponse("", trim($message));

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getTeacherVisions()
    {
        try {
            $visions = Vision::with(['visionAssigns.student']) // Eager load assigns + students
                ->whereHas('visionAssigns', function ($query) {
                    $query->where('teacher_id', Auth::id());
                })
                ->paginate(250);

            $response['visions'] = LaVisionResource::collection($visions)->response()->getData(true);
            return $this->sendResponse($response, "Teacher Visions");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getVisionParticipants(Request $request, Vision $Vision)
    {
        try {
            $visionAssignments = VisionAssign::where('teacher_id', Auth::id())
                ->where('vision_id', $Vision->id)
                ->orderByDesc('created_at')
                ->paginate(25);

            $students = LaVisionAssignResource::collection($visionAssignments)->response()->getData(true);

            return $this->sendResponse($students, "Teacher Vision Participants");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function approveRejectUserVision(Request $request, VisionQuestionAnswer $visionAnswer)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string',
                'status' => 'required|in:approved,rejected',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $user = $visionAnswer->user;
            $status = $request->status;
            $comment = $request->comment;

            if ($status === 'approved') {
                $visionAnswer->score = $visionAnswer->getPoints(); // store score
                $visionAnswer->approved($comment);

                if ($visionAnswer->is_first_attempt && $visionAnswer->score > 0) {
                    // ✅ Student reward
                    $user->createTransaction($visionAnswer, $visionAnswer->score, CoinTransaction::TYPE_VISION);

                    // ✅ Teacher reward (per correct submission)
                    $assign = \App\Models\VisionAssign::where('vision_id', $visionAnswer->vision_id)
                        ->where('student_id', $user->id)
                        ->first();

                    if ($assign && $assign->teacher_id) {
                        $teacher = \App\Models\User::find($assign->teacher_id);
                        $level = $visionAnswer->vision->laLevel ?? null;

                        $alreadyRewarded = \App\Models\CoinTransaction::where([
                            'user_id' => $teacher->id,
                            'coinable_type' => get_class($visionAnswer),
                            'coinable_id' => $visionAnswer->id,
                            'type' => CoinTransaction::TYPE_CORRECT_SUBMISSION,
                        ])->exists();

                        if (!$alreadyRewarded && $level && $level->teacher_correct_submission_points > 0) {
                            $teacher->createTransaction(
                                $visionAnswer,
                                $level->teacher_correct_submission_points,
                                CoinTransaction::TYPE_CORRECT_SUBMISSION
                            );
                        }
                    }
                }

                VisionUserStatus::updateOrCreate(
                    ['user_id' => $user->id, 'vision_id' => $visionAnswer->vision_id],
                    ['status' => 'completed']
                );

                $visionAnswer->vision->sendApproveNotification($user);
                $message = "Vision Approved";
            } else {
                $visionAnswer->rejected($comment);

                VisionUserStatus::updateOrCreate(
                    ['user_id' => $user->id, 'vision_id' => $visionAnswer->vision_id],
                    ['status' => 'rejected']
                );

                $visionAnswer->vision->sendRejectNotification($user);
                $message = "Vision Rejected";
            }

            return $this->sendResponse([
                'vision_answer_id' => $visionAnswer->id,
                'status' => $visionAnswer->status,
                'allocated_coins' => $visionAnswer->score ?? 0,
            ], $message);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getVisionAnswers(Request $request, Vision $vision, User $student)
    {
        try {
            $answers = VisionQuestionAnswer::with('question', 'media')
                ->where('user_id', $student->id)
                ->where('vision_id', $vision->id)
                ->get();

            if ($answers->isEmpty()) {
                return $this->sendError('No answers submitted by this student for this vision.');
            }

            $title = is_array($vision->title)
                ? ($vision->title['en'] ?? 'Untitled Vision')
                : (json_decode($vision->title, true)['en'] ?? 'Untitled Vision');

            $data = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_profile_image' => $student->profile_image_url,
                'class' => optional($student->grade)->name,
                'section' => optional($student->section)->name,
                'subject' => optional($vision->subject)->name,
                'vision_title' => $title,
                'submitted_at' => optional($answers->first())->created_at->format('Y-m-d H:i'),
                'answers' => [],
            ];

            foreach ($answers as $answer) {
                $questionText = optional($answer->question)->question;
                if (is_string($questionText)) {
                    $decoded = json_decode($questionText, true);
                    $questionText = is_array($decoded) ? ($decoded['en'] ?? $questionText) : $questionText;
                }

                $entry = [
                    'answer_id' => $answer->id,
                    'question_text' => $questionText,
                    'answer_type' => $answer->answer_type,
                    'status' => $answer->status,
                    'comment' => $answer->comment,
                    'score' => $answer->score,
                    'is_first_attempt' => $answer->is_first_attempt,
                ];

                if ($answer->answer_type === 'option') {
                    $entry['selected_option'] = $answer->answer_option;
                    $entry['correct_option'] = optional($answer->question)->correct_answer;
                    $entry['is_correct'] = strtolower($answer->answer_option) === strtolower(optional($answer->question)->correct_answer);
                } elseif ($answer->answer_type === 'text') {
                    $entry['answer_text'] = $answer->answer_text;
                } elseif ($answer->answer_type === 'image') {
                    $entry['media_id'] = $answer->media_id;
                    $entry['image_url'] = optional($answer->media)->full_url ?? null;
                    $entry['description'] = $answer->description;
                }

                $data['answers'][] = $entry;
            }

            return $this->sendResponse($data, 'All Vision Answers for Student');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getVisionParticipantsWithAnswers(Request $request, Vision $vision)
    {
        try {
            $classFilter = $request->input('class');
            
            $visionAssignments = VisionAssign::with('student.LaGrade', 'student.LaSection')
                ->where('teacher_id', Auth::id())
                ->where('vision_id', $vision->id)
                ->when($classFilter, function ($query) use ($classFilter) {
                    $query->whereHas('student.LaGrade', function ($q) use ($classFilter) {
                        $q->where('name', $classFilter)
                        ->orWhere('id', $classFilter);
                    });
                })
                ->orderByDesc('created_at')
                ->get();

            $studentsData = [];

            foreach ($visionAssignments as $assignment) {
                $student = $assignment->student;

                // Get answers for this student and vision
                $answers = VisionQuestionAnswer::with('question', 'media')
                    ->where('user_id', $student->id)
                    ->where('vision_id', $vision->id)
                    ->get();

                $submissionStatus = $answers->isNotEmpty() ? 'submitted' : 'assigned';

                $answerDetails = [];
                foreach ($answers as $answer) {
                    $questionText = optional($answer->question)->question;
                    if (is_string($questionText)) {
                        $decoded = json_decode($questionText, true);
                        $questionText = is_array($decoded) ? ($decoded['en'] ?? $questionText) : $questionText;
                    }

                    $entry = [
                        'answer_id' => $answer->id,
                        'question_text' => $questionText,
                        'answer_type' => $answer->answer_type,
                        'status' => $answer->status,
                        'comment' => $answer->comment,
                        'score' => $answer->score,
                        'is_first_attempt' => $answer->is_first_attempt,
                    ];

                    if ($answer->answer_type === 'option') {
                        $entry['selected_option'] = $answer->answer_option;
                        $entry['correct_option'] = optional($answer->question)->correct_answer;
                        $entry['is_correct'] = strtolower($answer->answer_option) === strtolower(optional($answer->question)->correct_answer);
                    } elseif ($answer->answer_type === 'text') {
                        $entry['answer_text'] = $answer->answer_text;
                    } elseif ($answer->answer_type === 'image') {
                        $entry['media_id'] = $answer->media_id;
                        $entry['image_url'] = optional($answer->media)->full_url ?? null;
                        $entry['description'] = $answer->description;
                    }

                    $answerDetails[] = $entry;
                }

                $studentsData[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'mobile_no' => $student->mobile_no,
                    'student_profile_image' => $student->profile_image_url,
                    'class' => optional($student->LaGrade)->name,
                    'section' => optional($student->LaSection)->name,
                    'assigned_at' => $assignment->created_at->format('Y-m-d H:i'),
                    'submission_status' => $submissionStatus,
                    'submitted_at' => $answers->first()?->created_at?->format('Y-m-d H:i'),
                    'answers' => $answerDetails,
                ];
            }

            $title = is_array($vision->title)
                ? ($vision->title['en'] ?? 'Untitled Vision')
                : (json_decode($vision->title, true)['en'] ?? 'Untitled Vision');

            $data = [
                'vision_id' => $vision->id,
                'vision_title' => $title,
                'subject' => optional($vision->subject)->name,
                'participants' => $studentsData,
            ];

            return $this->sendResponse($data, 'Vision Participants with Answers');

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function teacherLeaderboard(Request $request)
    {
        $teachers = User::with('school')
            ->where('type', 5) // Teacher type
            ->select('id', 'name', 'school_id', 'earn_coins', 'image_path') // include image_path
            ->orderByDesc('earn_coins')
            ->get();

        $data = $teachers->map(function ($teacher, $index) {
            return [
                'rank' => $index + 1,
                'teacher_id' => $teacher->id,
                'name' => $teacher->name,
                'school_name' => $teacher->school->name ?? 'N/A',
                'total_earned_coins' => $teacher->earn_coins,
                'profile_image' => $teacher->image_path ?? null, // ✨ raw relative path, let app add base URL
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function schoolLeaderboard()
    {
        // Step 1: Aggregate data from users table grouped by school
        $schoolStats = User::select(
                'school_id',
                DB::raw('SUM(earn_coins) as total_coins'),
                DB::raw('SUM(CASE WHEN type = 3 THEN 1 ELSE 0 END) as student_count'),
                DB::raw('SUM(CASE WHEN type = 5 THEN 1 ELSE 0 END) as teacher_count')
            )
            ->whereNotNull('school_id')
            ->groupBy('school_id')
            ->get();

        // Step 2: Fetch school names
        $schoolMap = School::pluck('name', 'id');

        // Step 3: Calculate S-Score and rank
        $ranked = $schoolStats->map(function ($row) use ($schoolMap) {
            $totalUsers = $row->student_count + $row->teacher_count;
            $sScore = $totalUsers > 0 ? round($row->total_coins / $totalUsers, 2) : 0;

            return [
                'school_id' => $row->school_id,
                'school_name' => $schoolMap[$row->school_id] ?? 'Unknown',
                'students' => $row->student_count,
                'teachers' => $row->teacher_count,
                'total_users' => $totalUsers,
                'total_coins' => $row->total_coins,
                's_score' => $sScore,
            ];
        })->sortByDesc('s_score')->values();

        // Step 4: Add rank
        $data = $ranked->map(function ($item, $index) {
            return array_merge($item, ['rank' => $index + 1]);
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

}