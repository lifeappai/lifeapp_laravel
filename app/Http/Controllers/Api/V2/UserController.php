<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\StatusEnum;
use App\Enums\SubjectEnum;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\MediaResource;
use App\Http\Resources\API\V1\UserWithFriendsResource;
use App\Http\Resources\API\V2\LaMissionCompleteResource;
use App\Http\Resources\API\V2\LaMissionResource;
use App\Http\Resources\API\V2\MentorSubjectResource;
use App\Http\Resources\API\V2\UserResource;
use App\Http\Traits\MediaUpload;
use App\Models\CoinTransaction;
use App\Models\LaMission;
use App\Models\LaMissionComplete;
use App\Models\MentorSubject;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends ResponseController
{

    use MediaUpload;
    public function dashboard()
    {
        try {
            $user  = Auth::user();
            $lastMissionComplete = LaMissionComplete::where('user_id', $user->id)->latest()->first();
            if (!$lastMissionComplete) {
                $user->last_mission = null;
            } else {
                $lastMission = LaMission::where('id', $lastMissionComplete->la_mission_id)->first();
                $user->last_mission = new LaMissionResource($lastMission);
            }
            $missions = LaMission::whereHas('subject', function ($query) {
                $query->where('is_coupon_available', StatusEnum::NO)->where('mission_status', SubjectEnum::MISSION_STATUS['YES']);
            })->whereDoesntHave('missionCompletes', function ($query) {
                $query->where('user_id', Auth::id());
            })->orderBy('index')->where('status', StatusEnum::ACTIVE)->limit(3)->get();
            $user->missions = LaMissionResource::collection($missions);
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
                'dob' => 'required',
                'gender' => 'required|integer',
                'grade' => 'required|integer',
                'address' => 'required',
                'mobile_no' => 'required|numeric',
                'state' => 'required',
                'city' => 'required',
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
                'subjects' =>  ['required'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $user->mentorSubjects()->delete();
            if ($request->subjects) {
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
}
