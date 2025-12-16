<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\StatusEnum;
use App\Enums\UserType;
use App\Http\Resources\API\V3\UserResource;
use App\Models\LaSection;
use App\Models\LaTeacherGrade;
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
                "type" => ['required', Rule::in([UserType::Student, UserType::Mentor, UserType::Teacher])],
                'name' => 'required_if:type,==,' . UserType::Student . ',' . UserType::Teacher,
                'la_grade_id' => ['required_if:type,==,' . UserType::Student , 'exists:la_grades,id'],
                'school' => 'required_if:type,==,' . UserType::Teacher,
                'section' => 'required_if:type,==,' . UserType::Student,
                'guardian_name' => 'nullable',
                'la_board_id' => ['nullable', 'exists:la_boards,id'],
                'gender' => ['nullable'],
                'state' => 'required_if:type,==,' . UserType::Student . ',' . UserType::Teacher,
                'city' => ['nullable'],
                //'city' => 'required_if:type,==,' . UserType::Student . ',' . UserType::Teacher,
                'subjects' =>  ['required_if:type,==,' . UserType::Mentor],
                'school_code' => 'nullable',
            );
            
            // Validate 'grades' only if 'type' is 'UserType::Teacher'
        if ($request->input('type') === UserType::Teacher) {
            $validate['grades'] = 'required|array';

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

            $data = $request->all();

            if ($request->has('school')) {
                $schoolValue = $request->get('school');
                if (intval($schoolValue)) {
                    $school = School::find($schoolValue);
                } else {
                    $appVisible = StatusEnum::ACTIVE;
                    if ($request->type == UserType::Teacher) {
                        $appVisible = StatusEnum::DEACTIVE;
                    }
                    $school = School::firstOrCreate([
                        'name' => $schoolValue,
                    ], [
                        'app_visible' => $appVisible,
                        'is_life_lab' => StatusEnum::DEACTIVE,
                        'state' => $request->state,
                        'city' => $request->city
                    ]);
                }
                $data['school_id'] = $school->id;
            }
            if ($request->has('section')) {
                $sectionValue = $request->get('section');
                $section = LaSection::updateOrCreate([
                    "name" => $sectionValue
                ]);
                $data['la_section_id'] = $section->id;
            }

            $user = User::where('mobile_no', $request->mobile_no)->first();
            if ($user) {
                if (($user->type != $request->type) && ($user->type != null)) {
                    return $this->sendError("Type Not Match");
                }
            }
            if ($request->type != UserType::Mentor) {
                if ($user) {
                    $user->update($data);
                } else {
                    $user = User::create($data);
                }
            }
            if ($request->type == UserType::Mentor) {
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

            if ($request->type == UserType::Teacher) {
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