<?php

namespace App\Http\Controllers\Api\V3;

use App\Enums\LessionPlanCategoryEnum;
use App\Enums\StatusEnum;
use App\Models\LaRequestGameEnrollment;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LaRequestGameEnrollmentController extends ResponseController
{
    public function requestGameEnrollment(Request $request)
    {
        try {
            $validate = array(
                'type' => ['required', Rule::in(LessionPlanCategoryEnum::Category)]
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $school = School::where('id', Auth::user()->school_id)->first();
            if (isset($school) && $school->is_life_lab == StatusEnum::ACTIVE) {
                return $this->sendError("Already Allocated Game Enrollment");
            }

            $checkGameEnrollment = LaRequestGameEnrollment::where('type', $request->type)->where('user_id', Auth::user()->id)->first();
            if ($checkGameEnrollment) {
                return $this->sendError("Already Requested For Enrollment");
            }

            LaRequestGameEnrollment::create([
                "type" => $request->type,
                "user_id" => Auth::user()->id,
            ]);

            return $this->sendResponse("", "Game Enrollment Requested Successfully");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
