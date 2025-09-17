<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V2\UserResource;
use App\Models\MentorSubject;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends ResponseController
{
    public function register(Request $request)
    {
        try {
            $validate = array(
                'mobile_no' => 'required',
                "type" => ['required', Rule::in([UserType::Student, UserType::Mentor])],
                'name' => 'required_if:type,==,' . UserType::Student,
                'grade' => ['required_if:type,==,' . UserType::Student, 'integer'],
                'school' => ['nullable'],
                'state' => 'required_if:type,==,' . UserType::Student,
                'city' => 'required_if:type,==,' . UserType::Student,
                'subjects' =>  ['required_if:type,==,' . UserType::Mentor],
            );

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $data = $request->all();

            if ($request->has('school')) {
                $schoolValue = $request->get('school');
                if (intval($schoolValue)) {
                    $school = School::find($schoolValue);
                } else {
                    $school = School::firstOrCreate([
                        'name' => $schoolValue,
                    ], [
                        'state' => $request->state,
                        'city' => $request->city
                    ]);
                }
                $data['school_id'] = $school->id;
            }

            $user = User::where('mobile_no', $request->mobile_no)->first();
            if ($request->type == UserType::Student) {
                if ($user) {
                    if (($user->type != UserType::Student) && ($user->type != null)) {
                        return $this->sendError("Only Student Can Register");
                    }
                    User::find($user->id)->update($data);
                    $user = User::find($user->id);
                } else {
                    $user = User::create($data);
                }
            }
            if ($request->type == UserType::Mentor) {
                if (($user->type != UserType::Mentor)) {
                    return $this->sendError("Only Mentor Can Login");
                }
                if ($request->subjects) {
                    $subjects = explode(",", $request->subjects);
                    foreach ($subjects as $subject) {
                        MentorSubject::create([
                            "user_id" => $user->id,
                            "la_subject_id" => $subject
                        ]);
                    }
                }
            }
            $user->device = $request->device;
            $user->device_token = $request->device_token;
            $user->save();

            $user->accessToken = $user->createToken('LifeApp')->accessToken;
            $response['user'] = new UserResource($user);
            return $this->sendResponse($response, "User Registered");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
