<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\UserType;
use App\Enums\StatusEnum;
use App\Enums\GameType;
use App\Http\Resources\API\V3\LaVisionResource;
use App\Http\Resources\API\V3\LaLevelResource;
use App\Models\Vision;
use App\Models\VisionQuestion;
use App\Models\VisionQuestionAnswer;
use App\Models\VisionUserStatus;
use App\Models\VisionAssign;
use App\Models\CoinTransaction;
use App\Models\Media;
use App\Models\User;
use App\Models\Lalevel;
use App\Models\LaCampaign;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaVisionController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            // 1) Map any aliases to their numeric IDs:
            $subjectMap = [
                'science' => 1,
                'maths'   => 2,
                'financial literacy' => 3,
            ];
            $rawId = $request->input('la_subject_id');
            if (is_string($rawId)) {
                $key = strtolower($rawId);
                if (isset($subjectMap[$key])) {
                    // overwrite with the corresponding integer ID
                    $request->merge(['la_subject_id' => $subjectMap[$key]]);
                }
            }

            // 2) Now validate, as before:
            $rules = [
                'la_subject_id' => ['required', 'exists:la_subjects,id'],
                'la_level_id'   => ['nullable', 'exists:la_levels,id'],
                'filter'        => ['nullable', Rule::in(['all', 'teacher_assigned', 'skipped', 'pending', 'completed', 'submitted'])],
                'search_lang'   => ['nullable'],
                'search_title'  => ['nullable'],
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            // 3) Build your base query:
            $visions = Vision::where('status', StatusEnum::ACTIVE)
                            ->where('la_subject_id', $request->la_subject_id);

            if ($request->filled('la_level_id')) {
                $visions->where('la_level_id', $request->la_level_id);
            }

            // 4) Access control
            $user = Auth::user();
            if (! $user) {
                return $this->sendError('Unauthenticated user.', 401);
            }
            if ($user->type == UserType::Teacher) {
                $visions->whereIn('allow_for', [
                    GameType::ALLOW_FOR['ALL'],
                    GameType::ALLOW_FOR['BY_TEACHER'],
                ]);
            } elseif ($user->type == UserType::Student) {
                $visions->where(function ($q) {
                    $q->whereIn('allow_for', [
                        GameType::ALLOW_FOR['ALL'],
                        GameType::ALLOW_FOR['BY_STUDENT'],
                    ])
                    ->orWhereHas('assignments', function ($q2) {
                        $q2->where('student_id', Auth::id());
                    });
                });
            }

            // 5) Apply filter tabs:
            if ($request->filter) {
                switch ($request->filter) {
                    case 'teacher_assigned':
                        $visions->whereHas('assignments', function ($q) {
                            $q->where('student_id', Auth::id());
                        });
                        break;

                    case 'completed':
                    case 'pending':
                    case 'skipped':
                    case 'submitted':
                        $visions->whereHas('userStatus', function ($q) use ($request) {
                            $q->where('status', $request->filter);
                        });
                        break;
                }
            }

            // 6) JSON-title search:
            if ($request->filled('search_title')) {
                $searchTitle = strtolower($request->search_title);

                $visions->where(function ($query) use ($searchTitle, $request) {
                    if ($request->filled('search_lang')) {
                        // Search only in a specific language key
                        $lang = $request->search_lang;
                        $query->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(title, '$.\"{$lang}\"'))) LIKE ?", ["%$searchTitle%"]);
                    } else {
                        // Search across all keys in the JSON
                        $query->whereRaw("JSON_SEARCH(LOWER(title), 'all', ?) IS NOT NULL", ["%$searchTitle%"])
                              ->orWhereRaw("JSON_SEARCH(LOWER(description), 'all', ?) IS NOT NULL", ["%$searchTitle%"]);
                    }
                });
            }

            // 7) Final pagination & response:
            $visions = $visions->orderBy('index')->paginate(15);
            $response['visions'] = LaVisionResource::collection($visions)
                                    ->response()
                                    ->getData(true);

            return $this->sendResponse($response, "Visions list");

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    
    public function show($id)
    {
        try {
            $vision = Vision::where('status', StatusEnum::ACTIVE)
                ->where('id', $id)
                ->withCount([
                    'questions',
                    'questionAnswers as answered_count' => function ($query) {
                        $query->where('user_id', Auth::id());
                    }
                ])
                ->first();

            if (!$vision) {
                return $this->sendError("Vision not found or inactive.");
            }

            return $this->sendResponse(new LaVisionResource($vision), 'Vision details');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getVisionQuestions($id)
    {
        $questions = VisionQuestion::where('vision_id', $id)
            ->orderBy('question_index')
            ->get()
            ->map(function ($question) {
                $vision = $question->vision()->with('laLevel')->first();

                return [
                    'id' => $question->id,
                    'question' => json_decode($question->question, true),
                    'question_type' => $question->question_type,
                    'options' => $question->options ? json_decode($question->options, true) : null,
                    'correct_answer' => $question->correct_answer,
                    'question_index' => $question->question_index,
                    'level' => $vision && $vision->laLevel ? new LaLevelResource($vision->laLevel) : [],
                ];
            });

        return response()->json([
            'status' => 200,
            'data' => $questions,
            'message' => 'Vision questions'
        ]);
    }

    public function completeVision(Request $request)
    {
        try {
            $validate = [
                'vision_id'     => ['required', 'exists:visions,id'],
                'timing'        => ['required'],
                'answer_type'   => ['required', 'in:option,text,image'],
            ];

            if ($request->answer_type === 'option') {
                $validate['answers'] = ['required', 'array', 'min:1'];
                $validate['answers.*.question_id'] = ['required', 'exists:vision_questions,id'];
                $validate['answers.*.answer_option'] = ['required'];
            } elseif ($request->answer_type === 'text') {
                $validate['question_id'] = ['required', 'exists:vision_questions,id'];
                $validate['answer_text'] = ['required'];
            } elseif ($request->answer_type === 'image') {
                $validate['question_id'] = ['required', 'exists:vision_questions,id'];
                $validate['media'] = ['required', 'file'];
                $validate['description'] = ['nullable'];
            }

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), 400);
            }

            $userId = Auth::id();
            $visionId = $request->vision_id;
            $answerType = $request->answer_type;

            // Get previous attempts
            $attemptCount = VisionQuestionAnswer::where('user_id', $userId)
                ->where('vision_id', $visionId)
                ->where('answer_type', $answerType)
                ->max('attempt_number') ?? 0;

            $isFirstAttempt = $attemptCount === 0;
            $newAttempt = $attemptCount + 1;
            $earnedCoins = 0;

            if ($answerType === 'option') {
                $correctCount = 0;
                $totalQuestions = 0;

                foreach ($request->answers as $answer) {
                    $question = VisionQuestion::find($answer['question_id']);
                    $totalQuestions++;

                    $isCorrect = strtolower($answer['answer_option']) === strtolower(optional($question)->correct_answer);
                    $score = ($isFirstAttempt && $isCorrect)
                        ? ($question->vision->laLevel->vision_mcq_points ?? 0)
                        : 0;

                    $visionAnswer = VisionQuestionAnswer::create([
                        'user_id'         => $userId,
                        'vision_id'       => $visionId,
                        'question_id'     => $answer['question_id'],
                        'answer_option'   => $answer['answer_option'],
                        'answer_type'     => 'option',
                        'timing'          => $request->timing,
                        'attempt_number'  => $newAttempt,
                        'is_first_attempt'=> $isFirstAttempt,
                        'score'           => $score,
                        'status'          => 'approved',
                    ]);

                    if ($score > 0) {
                        Auth::user()->createTransaction($visionAnswer, $score, CoinTransaction::TYPE_VISION);
                        $correctCount++;
                    }
                }

                // ✅ Reward assigning teacher (only if first attempt and at least 1 correct)
                if ($isFirstAttempt && $correctCount > 0 && $totalQuestions > 0) {
                    $assign = \App\Models\VisionAssign::where('vision_id', $visionId)
                        ->where('student_id', $userId)
                        ->first();

                    if ($assign && $assign->teacher_id) {
                        $teacher = \App\Models\User::find($assign->teacher_id);
                        $vision = \App\Models\Vision::find($visionId);
                        $level = $vision->laLevel ?? null;

                        if ($level && $level->teacher_correct_submission_points > 0) {
                            $reward = round(($correctCount / $totalQuestions) * $level->teacher_correct_submission_points, 2);

                            if ($reward > 0) {
                                $teacher->createTransaction(
                                    $vision,
                                    $reward,
                                    \App\Models\CoinTransaction::TYPE_CORRECT_SUBMISSION
                                );
                            }
                        }
                    }
                }

                VisionUserStatus::updateOrCreate(
                    ['user_id' => $userId, 'vision_id' => $visionId],
                    ['status' => 'completed']
                );

                return response()->json([
                    'message' => 'Vision completed successfully.',
                    'coins_earned' => $earnedCoins,
                ]);
            }


            // Reflection (Text)
            if ($answerType === 'text') {
                VisionQuestionAnswer::create([
                    'user_id'     => $userId,
                    'vision_id'   => $visionId,
                    'question_id' => $request->question_id,
                    'answer_text' => $request->answer_text,
                    'answer_type' => 'text',
                    'timing'      => $request->timing,
                    'attempt_number' => $newAttempt,
                    'is_first_attempt' => $isFirstAttempt,
                    'score' => 0,
                ]);
            }

            // Image Upload
            if ($answerType === 'image') {
                $recentAnswer = VisionQuestionAnswer::where([
                    'user_id' => $userId,
                    'vision_id' => $visionId,
                    'question_id' => $request->question_id,
                    'answer_type' => 'image',
                ])
                ->where('created_at', '>=', now()->subSeconds(10)) 
                ->exists();

                if ($recentAnswer) {
                    return response()->json([
                        'message' => 'Vision Already Submitted.',
                    ], 200);
                }

                $mediaFile = $request->file('media');
                $fileName = $mediaFile->getClientOriginalName();
                $filePath = Storage::put('media', $mediaFile);

                $media = Media::create([
                    'name' => $fileName,
                    'path' => $filePath,
                ]);

                VisionQuestionAnswer::create([
                    'user_id'     => $userId,
                    'vision_id'   => $visionId,
                    'question_id' => $request->question_id,
                    'media_id'    => $media->id,
                    'description' => $request->input('description'),
                    'answer_type' => 'image',
                    'timing'      => $request->timing,
                    'attempt_number' => $newAttempt,
                    'is_first_attempt' => $isFirstAttempt,
                    'score' => 0,
                ]);
            }

            VisionUserStatus::updateOrCreate(
                ['user_id' => $userId, 'vision_id' => $visionId],
                ['status' => 'submitted']
            );

            return response()->json([
                'message' => 'Vision submitted for review.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    public function getVisionResult(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vision_id' => ['required', 'exists:visions,id'],
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $userId = Auth::id();
            $visionId = $request->vision_id;

            // Fetch one of the user's submitted answers for this vision
            $answer = VisionQuestionAnswer::where('user_id', $userId)
                ->where('vision_id', $visionId)
                ->first();

            if (!$answer) {
                return $this->sendError("No submission found for this vision.");
            }

            $responseData = [
                'vision_id' => $visionId,
                'answer_type' => $answer->answer_type,
            ];

            if ($answer->answer_type === 'option') {
                // MCQ type – show earned coins immediately
                $totalCoins = VisionQuestionAnswer::where('user_id', $userId)
                    ->where('vision_id', $visionId)
                    ->where('answer_type', 'option')
                    ->sum('score');

                $responseData['status'] = 'approved'; // always approved immediately
                $responseData['earned_coins'] = $totalCoins;
                $responseData['message'] = "You earned $totalCoins coins";

            } elseif (in_array($answer->answer_type, ['text', 'image'])) {
                // Reflection or image – show result only if reviewed
                if ($answer->approved_at) {
                    $responseData['status'] = 'approved';
                    $responseData['earned_coins'] = $answer->score;
                    $responseData['message'] = "Your answer has been approved. You earned {$answer->score} coins.";
                } elseif ($answer->rejected_at) {
                    $responseData['status'] = 'rejected';
                    $responseData['earned_coins'] = 0;
                    $responseData['message'] = "Your answer was rejected.";
                } else {
                    $responseData['status'] = 'in_review';
                    $responseData['earned_coins'] = 0;
                    $responseData['message'] = "Your submission is still under review.";
                }
            } else {
                return $this->sendError("Invalid answer type.");
            }

            return $this->sendResponse($responseData, "Vision result fetched successfully");

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateUserStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vision_id' => 'required|exists:visions,id',
                'status' => 'required|in:pending,skipped',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $visionId = $request->vision_id;
            $status = $request->status;
            $userId = Auth::id();

            $userStatus = VisionUserStatus::updateOrCreate(
                ['user_id' => $userId, 'vision_id' => $visionId],
                ['status' => $status]
            );

            return $this->sendResponse($userStatus, 'Vision status updated.');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function skipVision(Request $request)
    {
        $request->validate([
            'vision_id' => 'required|exists:visions,id',
        ]);

        $userId = Auth::id();
        $visionId = $request->vision_id;

        $vision = Vision::find($visionId);

        // Check if already completed or skipped
        $existing = VisionUserStatus::where('user_id', $userId)
            ->where('vision_id', $visionId)
            ->first();

        if ($existing && in_array($existing->status, ['completed', 'skipped'])) {
            return response()->json([
                'message' => 'Vision already ' . $existing->status
            ], 200);
        }

        // Mark the current vision as skipped
        VisionUserStatus::updateOrCreate(
            ['user_id' => $userId, 'vision_id' => $visionId],
            ['status' => 'skipped']
        );

        // Find another vision (same subject & level) not completed or skipped
        $nextVision = Vision::where('status', StatusEnum::ACTIVE)
            ->where('la_subject_id', $vision->la_subject_id)
            ->where('la_level_id', $vision->la_level_id)
            ->where('id', '!=', $visionId)
            ->whereDoesntHave('userStatuses', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                ->whereIn('status', ['completed', 'skipped']);
            })
            ->inRandomOrder()
            ->first();

        if ($nextVision) {
            return response()->json([
                'message' => 'Vision skipped. Here is another vision.',
                'next_vision' => new LaVisionResource($nextVision)
            ]);
        }

        return response()->json([
            'message' => 'Vision skipped. No more available visions for this subject and level.'
        ]);
    }

    public function markVisionPending(Request $request)
    {
        $request->validate([
            'vision_id' => 'required|exists:visions,id',
        ]);

        $userId = Auth::id();
        $visionId = $request->vision_id;

        // Do not overwrite if already completed
        $existing = VisionUserStatus::where('user_id', $userId)
            ->where('vision_id', $visionId)
            ->first();

        if ($existing && $existing->status === 'completed') {
            return response()->json([
                'message' => 'Vision already completed'
            ], 200);
        }

        VisionUserStatus::updateOrCreate(
            ['user_id' => $userId, 'vision_id' => $visionId],
            ['status' => 'pending']
        );

        return response()->json([
            'message' => 'Vision marked as pending.'
        ]);
    }

    public function viewVisionAnswers(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vision_id' => 'required|exists:visions,id',
            ]);
    
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first(), 400);
            }
    
            $userId = Auth::id();
            $visionId = $request->vision_id;
    
            $answers = VisionQuestionAnswer::where('user_id', $userId)
                ->where('vision_id', $visionId)
                ->with('media') // eager load media relation
                ->get();
    
            $response = [];
    
            foreach ($answers as $answer) {
                $question = VisionQuestion::find($answer->question_id);
    
                $data = [
                    'question_id'   => $answer->question_id,
                    'question_text' => is_string($question->question) ? json_decode($question->question, true) : $question->question,
                    'answer_type'   => $answer->answer_type,
                ];
    
                if ($answer->answer_type === 'option') {
                    $data['selected_option'] = $answer->answer_option;
                    $data['is_correct'] = strtolower(trim($question->correct_answer)) === strtolower(trim($answer->answer_option));
                    if (!$data['is_correct']) {
                        $data['correct_option'] = $question->correct_answer;
                    }
    
                } elseif ($answer->answer_type === 'text') {
                    $data['answer_text'] = $answer->answer_text;
                    $data['status'] = $answer->status;
                    $data['comment'] = $answer->comment;
                    $data['score'] = $answer->score;
    
                } elseif ($answer->answer_type === 'image') {
                    $data['description'] = $answer->description;
                    $data['status'] = $answer->status;
                    $data['comment'] = $answer->comment;
                    $data['score'] = $answer->score;
                    $data['image_url'] = $answer->media ? Storage::url($answer->media->path) : null;
                }
    
                $response[] = $data;
            }
    
            return $this->sendResponse($response, 'Vision answers retrieved successfully.');
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function notifyVisionStatus(Request $request, $visionId)
    {
        $request->validate([
            'status'   => 'required|in:approved,rejected',
            'user_id'  => 'required|integer|exists:users,id',
        ]);

        $vision = Vision::findOrFail($visionId);
        $user   = User::findOrFail($request->user_id);

        if ($request->status === 'approved') {
            $vision->sendApproveNotification($user);
        } else {
            $vision->sendRejectNotification($user);
        }

        return response()->json(['success' => true]);
    }
    
}