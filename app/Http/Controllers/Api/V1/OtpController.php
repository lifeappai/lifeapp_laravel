<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicUserResrouce;
use App\Models\ForgetPinRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Requests\Auth\V1\OtpRequest;
use App\Http\Requests\Auth\V1\ConfirmOtpRequest;
use App\Models\User;
use App\Http\Traits\CodeTrait;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class OtpController extends Controller
{
	use CodeTrait;

	// New Send Otp API

	/**
	 * @throws \Throwable
	 */
	public function sendOtp(OtpRequest $request)
	{
        Log::info("OTP Request");

		$data = $request->validated();

		$response_data = $this->sentOtp('91', $data['mobile_no']);
		throw_if(
			$response_data['type'] !== 'success',
			new \ErrorException('Not able to sent otp', Response::HTTP_SERVICE_UNAVAILABLE)
		);

		return new JsonResponse($response_data, Response::HTTP_OK);
    }

    public function forgetPin(Request $request)
    {
	    $data = $request->validate([
	        'id' => 'required'
        ]);

        $response_data = $this->sentOtp('91', $request->mobile_no);
        throw_if(
            $response_data['type'] !== 'success',
            new \ErrorException('Not able to sent otp', Response::HTTP_SERVICE_UNAVAILABLE)
        );

        ForgetPinRequest::create([
            "user_id" => $data['id'],
            'mobile_no' => $request->mobile_no,
        ]);

        return new JsonResponse($response_data, Response::HTTP_OK);
    }

    public function resetPin(Request $request)
    {
        $data = $request->validate([
            'otp' => 'required',
            'pin' => 'required|string|min:4'
        ]);

        $response_data = $this->verifyOtp('91', $request->mobile_no, $data['otp']);

        throw_if($response_data['type'] !== 'success',
            new \ErrorException('Otp invalid or expired', Response::HTTP_UNPROCESSABLE_ENTITY)
        );

        $forgetPinRequest = ForgetPinRequest::where('mobile_no', $request->mobile_no)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if ($forgetPinRequest) {
            $user = $forgetPinRequest->user;
            $user->password = Hash::make($data['pin']);
            $user->save();

            $forgetPinRequest->verified_at = Carbon::now();
            $forgetPinRequest->save();

            return response()->json([
                'message' => 'Pin has been reset successfully.',
            ]);
        }

        return response()->json([
            'error' => 'Otp invalid or expired',
        ], 422);
    }


    /**
     * @param ConfirmOtpRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
	public function confirmOtp(ConfirmOtpRequest $request)
	{
		$data = $request->validated();
		try {

		    if (env('APP_ENV') !== 'local') {
                $response_data = $this->verifyOtp('91', $data['mobile_no'], $data['otp']);

                throw_if($response_data['type'] !== 'success',
                    new \ErrorException('Otp invalid or expired', Response::HTTP_UNPROCESSABLE_ENTITY)
                );
            }

            $users = User::where('mobile_no', $data['mobile_no'])->get();

		    $token = Crypt::encryptString($data['mobile_no'] . "." . time());

            $msg = [
                'msg' => 'Otp verified',
                'x-master-token' => $token,
                'users' => PublicUserResrouce::collection($users)
            ];

            return new JsonResponse($msg, Response::HTTP_OK);
        } catch (\Exception $exception) {
		    return response()->json([
		        'error' => 'Otp invalid or expired',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
	}
}
