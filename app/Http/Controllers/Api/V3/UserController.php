<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Http\Resources\API\V1\MediaResource;
use App\Http\Resources\API\V1\UserWithFriendsResource;
use App\Http\Resources\API\V3\LaMissionResource;
use App\Http\Resources\API\V3\MentorSubjectResource;
use App\Http\Resources\API\V3\UserResource;
use App\Http\Traits\MediaUpload;
use App\Models\CoinTransaction;
use App\Models\LaMission;
use App\Models\LaSection;
use App\Models\LaTeacherGrade;
use App\Models\MentorSubject;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends ResponseController
{

    use MediaUpload;
    public function dashboard()
    {
        try {
            $user  = Auth::user();
            $baloonCarMission = LaMission::where('id', 1)->first();
            $user->baloonCarMission = new LaMissionResource($baloonCarMission);
            $user->userRank = $user->user_rank;
            $user = new UserResource(Auth::user());
            $response['user'] = $user;
            return $this->sendResponse($response, "Dashboard");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getUsers(Request $request)
    {
        try {
            $query = User::query();
            $search = $request->get('search');
            $query->where(function ($query) use ($search) {
                $query->where('type', UserType::Student)->orWhere('type', null);
            });
            if (!empty($search)) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('mobile_no', 'like', '%' . $search . '%');
                });
            }
            $users = UserWithFriendsResource::collection($query->get());
            return $this->sendResponse($users, "Users");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateProfileImage(Request $request)
    {
        try {
            $validate = array(
                'media'   => 'required|mimes:jpeg,png,jpg|max:10240',
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $media = $this->upload($request['media']);
            $user = $request->user();
            $user->profile_image = $media->name;
            $user->image_path = $media->path;
            $user->save();
            $data = new MediaResource($media);
            return $this->sendResponse($data, "Users");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $validate = array(
                'school_id' => 'integer',
                'name' => 'required',
                'student_id' => 'integer',
                'dob' => 'nullable',

                'gender' => 'nullable',
                'la_grade_id' => 'required|exists:la_grades,id',
                'address' => 'nullable',
                'state' => 'required',
                'city' => 'required',
                'school_code' => 'nullable',
                'section' => 'required_if:type,==,' . UserType::Student  . ',' . UserType::Teacher,
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $user = $request->user();
            $data = $request->all();
            if ($request->has('school')) {
                $school = School::firstOrCreate(
                    [
                        'name' => $request->get('school'),
                        'state' => $request->state,
                        'city' => $request->city
                    ]
                );
                $data['school_id'] = $school->id;
            }

            if ($request->has('section')) {
                $sectionValue = $request->get('section');
                $section = LaSection::updateOrCreate([
                    "name" => $sectionValue
                ]);
                $data['la_section_id'] = $section->id;
            }

            $user->update($data);

            $data = new UserResource($user);
            return $this->sendResponse($data, "Profile Update Successfully");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function mentorProfile(Request $request)
    {
        try {
            $user = $request->user();
            $validate = array(
                'name' => 'required',
                'mobile_no' => ['required', Rule::unique('users')->ignore($user->id)],
                'subjects' =>  ['nullable'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            if ($request->subjects) {
                $user->mentorSubjects()->delete();
                $subjects = explode(",", $request->subjects);
                foreach ($subjects as $subject) {
                    MentorSubject::create([
                        "user_id" => $user->id,
                        "la_subject_id" => $subject
                    ]);
                }
            }
            $mentorSubjects = MentorSubjectResource::collection($user->mentorSubjects);
            $data = $request->all();
            $user->update($data);
            $response = [];
            $response['name'] = $user->name;
            $response['mobile_no'] = $user->mobile_no;
            $response['subjects'] = $mentorSubjects;
            return $this->sendResponse($response, "Mentor Profile Update Successfully");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getCoinTransactions(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->type == UserType::Admin) {
                $validate = array(
                    'user_id' => ['required', 'exists:users,id'],
                );
                $validator = Validator::make($request->all(), $validate);
                if ($validator->fails()) {
                    return $this->sendError($validator->errors()->first());
                }
                $user = User::find($request->user_id);
            }
            $coinData = $user->coinTransactions()->where('type', '!=', CoinTransaction::TYPE_ADMIN)
                ->has('coinable')
                ->with('coinable')
                ->latest()->paginate(15);

            return $this->sendResponse($coinData, "Coin History");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function getNotifications(Request $request)
    {
        try {
            $notifications = $request->user()->notifications;
            return $this->sendResponse($notifications, "Notifications");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteNotifications(Request $request)
    {
        try {
            $request->user()->notifications()->delete();
            return $this->sendResponse("", "Notification has bean cleared");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function readNotifications(Request $request)
    {
        try {
            $request->user()->unreadNotifications->markAsRead();
            return $this->sendResponse("", "Notification has bean read");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function updateTeacherProfile(Request $request)
    {
        try{
            $validate = array(
                'name' => 'nullable',
                'mobile_no' => 'nullable|integer',
                'school_id' => 'nullable|exists:schools,id',
                "type" => ['required', Rule::in(UserType::Teacher)],
                'grades' => 'nullable|array',
                'dob' => 'nullable|date',  //added for date of birth
                'la_board_id' => 'nullable|exists:la_boards,id', //added for boardid
                'board_name' => 'nullable|string', //added for boardname
            );

            if (isset($request->grades)) {
                $validate['grades.*'] = function ($attribute, $value, $fail) {
                    if (!isset($value['la_grade_id'])) {
                        $fail('Each item in grades must have la_grade_id.');
                    }
                    if (!isset($value['la_section_id'])) {
                        $fail('Each item in grades must have la_section_id.');
                    }
                    if (!isset($value['subjects'])) {
                        $fail('Each item in grades must have subjects.');
                    }
                };

                $validate['grades.*.la_grade_id'] = ['required', 'exists:la_grades,id'];
                $validate['grades.*.la_section_id'] = ['required', 'exists:la_sections,id'];
                $validate['grades.*.subjects'] = ['required', 'exists:la_subjects,id'];
            }

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $user = $request->user();
            $updateData = [
                'name' => $request->name,
                'mobile_no' => $request->mobile_no,
                'school_id' => $request->school_id,
                'dob' => $request->dob, //added for date of birth
                'la_board_id' => $request->la_board_id, //added for boardid
                'board_name' => $request->board_name //added for boardname
            ];

            $updateData = array_filter($updateData, function ($value) {
                return (!is_array($value) && !is_null($value)) || (is_array($value) && !empty($value));
            });

            if(!empty($updateData)){
                $user->update($updateData);
            }

            if ($request->grades){
                $user->laTeacherGrades()->delete();
            foreach($request->grades as $grade){
                $laGradeId = $grade['la_grade_id'];
                $laSectionId = $grade['la_section_id'];
                $subjectId = $grade['subjects'];

                LaTeacherGrade::create([
                    'user_id' => $user->id,
                    'la_subject_id' => $subjectId,
                    'la_grade_id' => $laGradeId,
                    'la_section_id' => $laSectionId,
                ]);
            }
        }
        $data = new UserResource($user);

        return $this->sendResponse($data, "Teacher Profile Update Successfully");

        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function TeacherSubjectGrade()
    {
        $teacherId = Auth::id();

        // Fetch teacher's grades with related subject & grade
        $teacherGrades = LaTeacherGrade::with(['laSubject', 'laGrade'])
            ->where('user_id', $teacherId)
            ->get();

        // Map subject-grade pairs
        $subjectGradePairs = $teacherGrades
            ->filter(fn($tg) => !empty($tg->laSubject) && !empty($tg->laGrade))
            ->map(function ($tg) {
                return [
                    'subject_id' => $tg->laSubject->id,
                    'subject_title' => $tg->laSubject->default_title,
                    'grade_id' => $tg->laGrade->id,
                    'grade_name' => $tg->laGrade->name,
                ];
            })
            ->unique(fn($item) => $item['subject_id'] . '-' . $item['grade_id']) // avoid duplicates
            ->values();

        return response()->json([
            'status' => true,
            'subject_grade_pairs' => $subjectGradePairs,
        ]);
    }

}