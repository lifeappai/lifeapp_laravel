<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V2\LaSubjectResource;
use App\Models\LaSubject;
use App\Models\LaSubjectCouponCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LaSubjectController extends ResponseController
{
    public function index(Request $request)
    {
        try {
            $requestCount = Cache::get("load_request_count", 1);
            Log::info("SUBJECT API LOAD TEST: " . $requestCount);
            Cache::put("load_request_count",  $requestCount + 1);

            $subjects = LaSubject::orderBy('index')->where('status', StatusEnum::ACTIVE);
            if ($request->type) {
                if ($request->type == 1) {
                    $subjects->where('mission_status', StatusEnum::ACTIVE);
                }
                if ($request->type == 2) {
                    $subjects->where('quiz_status', StatusEnum::ACTIVE);
                }
            }
            $subjects = $subjects->get();
            $response['subject'] = LaSubjectResource::collection($subjects);
            return $this->sendResponse($response, "Subjects");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    public function assignCouponCodeToUser(LaSubject $laSubject, Request $request)
    {
        try {
            $validate = array(
                'coupon_code' => ['required'],
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $checkCouponCode = LaSubjectCouponCode::where('coupon_code', $request->coupon_code)->first();
            if (!$checkCouponCode) {
                return $this->sendError("Subject Coupon Code Doesn't Exist");
            }
            if ($checkCouponCode->la_subject_id != $laSubject->id) {
                return $this->sendError("Subject Coupon Code Doesn't Belongs For This Subject");
            }
            if ($checkCouponCode->user_id != null) {
                return $this->sendError("Subject Coupon Code Already Used, Try with Another Coupon");
            }
            $checkAnotherCouponAdd = LaSubjectCouponCode::where('user_id', Auth::user()->id)->where('la_subject_id', $laSubject->id)->first();
            if ($checkAnotherCouponAdd) {
                return $this->sendError("User Already Added Same Subject Coupon Code");
            }
            $checkCouponCode->user_id = Auth::user()->id;
            $checkCouponCode->unlock_coupon_at = Carbon::now()->toDateTimeString();
            $checkCouponCode->save();
            return $this->sendResponse("", "Assign User To Subject Coupon");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
