<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V2\MentorSubjectResource;
use App\Http\Traits\CodeTrait;
use App\Models\MentorSubject;
use App\Models\OtpRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OtpController extends ResponseController
{
    use CodeTrait;

    public function sendOtp(Request $request)
    {
        try {
            $validate = array(
                "type" => ['required', Rule::in([UserType::Student, UserType::Mentor])],
                'mobile_no' => 'required_if:type,==,' . UserType::Student,
                "mentor_code" =>  'required_if:type,==,' . UserType::Mentor,
            );

            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $mobileNumber = $request->mobile_no;
            if ($request->type == UserType::Mentor) {
                $checkMentorCode = User::where('pin', $request->mentor_code)->first();
                if (!$checkMentorCode) {
                    return $this->sendError("Code Is Invalid");
                }
                $mobileNumber = $checkMentorCode->mobile_no;
            }

            $otpRequest = OtpRequest::create([
                "mobile_no" => $mobileNumber,
                "type" => $request->type,
            ]);
            $response_data = $this->sentOtp('91', $mobileNumber);

            if ($response_data['type'] !== 'success') {
                return $this->sendError('Not able to sent otp');
            }
            $response_data['mobile_no'] = $mobileNumber;
            $otpRequest->request_id  = isset($response_data['request_id']) ? $response_data['request_id'] : "";
            $otpRequest->status = 1;
            $otpRequest->save();
            return $this->sendResponse($response_data, "OTP Sent");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function confirmOtp(Request $request)
    {
        try {
            $validate = array(
                'mobile_no' => 'required',
                'otp' => 'required',
                "type" => ['required', Rule::in([UserType::Student, UserType::Mentor])],
                "device" => 'in:ios,android',

            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $response_data = $this->verifyOtp('91', $request->mobile_no, $request->otp);
            if ($response_data['type'] !== 'success') {
                return $this->sendError('Otp invalid or expired');
            }
            $otpRequest = OtpRequest::where('mobile_no', $request->mobile_no)->latest()->first();
            if ($otpRequest) {
                $otpRequest->veified_at = Carbon::now()->toDateTimeString();
                $otpRequest->status = $response_data['type'] === 'success' ? 1 : -1;
                $otpRequest->save();
            }
            $user = User::where('mobile_no', $request->mobile_no)->first();

            $token = "";
            $mentorSubjects = [];
            if ($user) {
                $token = $user->createToken('LifeApp')->accessToken;
                if ($request->type == UserType::Student) {
                    if (($user->type != UserType::Student) && ($user->type != null)) {
                        return $this->sendError("Only Student Can Regiter");
                    }
                }
                if ($request->type == UserType::Mentor) {
                    if (($user->type != UserType::Mentor)) {
                        return $this->sendError("Only Mentor Can Login");
                    }
                    $mentorSubjects = MentorSubjectResource::collection($user->mentorSubjects);
                }
                $user->device = $request->device;
                $user->device_token = $request->device_token;
                $user->save();
            }

            $response_data['token'] = $token;
            $response_data['subjects'] = $mentorSubjects;
            return $this->sendResponse($response_data, "OTP Verified");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $validate = array(
                'mobile_no' => 'required',
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }
            $response_data = $this->otpResend('91', $request->mobile_no);
            if ($response_data['type'] !== 'success') {
                return $this->sendError('Otp invalid or expired');
            }
            return $this->sendResponse($response_data, "OTP Resend");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
