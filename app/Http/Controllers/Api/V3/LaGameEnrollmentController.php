<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\GameEnrollmentTypeEnum;
use App\Enums\StatusEnum;
use App\Models\LaGameEnrollment;
use App\Models\LaRequestGameEnrollment;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaGameEnrollmentController extends ResponseController
{
    public function assignGameEnrollmentToUser(Request $request)
    {
        try {
            $validate = array(
                'enrollment_code' => ['required'],
                'type' => ['required', Rule::in(GameEnrollmentTypeEnum::TYPE)]
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $checkEnrollmentCode = LaGameEnrollment::where('enrollment_code', $request->enrollment_code)->first();
            if (!$checkEnrollmentCode) {
                return $this->sendError("Enrollment Code Doesn't Exist");
            }
            if ($checkEnrollmentCode->type != $request->type) {
                return $this->sendError("Enrollment Code Doesn't Belongs For This Type");
            }
            if ($checkEnrollmentCode->user_id != null) {
                return $this->sendError("Enrollment Code Already Used, Try with Another Coupon");
            }
            $checkAnotherCouponAdd = LaGameEnrollment::where('user_id', Auth::user()->id)->where('type', $request->type)->first();
            if ($checkAnotherCouponAdd) {
                return $this->sendError("User Already Added Enrollment Code");
            }
            $checkEnrollmentCode->user_id = Auth::user()->id;
            $checkEnrollmentCode->unlock_enrollment_at = Carbon::now()->toDateTimeString();
            $checkEnrollmentCode->save();
            return $this->sendResponse("", "Assign User To Enrollment Code");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function checkGameEnrollments()
    {
        try {
            $response = [];
            $school = School::where('id', Auth::user()->school_id)->first();
            if (isset($school) && $school->is_life_lab == StatusEnum::ACTIVE) {
                foreach (GameEnrollmentTypeEnum::TYPE as $key => $type) {
                    $response[$key] = GameEnrollmentTypeEnum::ACCEPTED;
                }
            } else {
                foreach (GameEnrollmentTypeEnum::TYPE as $key => $type) {
                    $response[$key] = GameEnrollmentTypeEnum::NOT_REQUESTED;
                    $checkGameEnrollment = LaGameEnrollment::where('type', $type)->where('user_id', Auth::user()->id)->first();
                    if ($checkGameEnrollment) {
                        $response[$key] = GameEnrollmentTypeEnum::ACCEPTED;
                    } else {
                        $checkGameRequested = LaRequestGameEnrollment::where('type', $type)->where('user_id', Auth::user()->id)->whereNull('approved_at')->first();
                        if ($checkGameRequested) {
                            $response[$key] = GameEnrollmentTypeEnum::REQUESTED;
                        }
                    }
                }
            }
            return $this->sendResponse($response, "Check User Game Enrollments");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
