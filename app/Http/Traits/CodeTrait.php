<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\Constants\RatingMultiplier;

trait CodeTrait
{

    public function sentOtp(string $country_code, int $mobile)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('msg91.send_otp_url'), [
                "template_id" => config('msg91.template_id'),
                'mobile' => sprintf('%s%s', $country_code, $mobile),
                'authkey' => config('msg91.auth_key'),
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::critical($e->getMessage(), ['response' => $response]);
            throw $e;
        }
    }

    /**
     * @param string $id
     * @param int $code
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function verifyOtp(string $country_code, int $mobile, int $otp)
    {
        try {
            
            $allowedTestNumbers = [
                "8900000001",
                "8900000002",
                "8900000003",
                "8293512602",
            ];

            if ($otp == '8712') {
                if (in_array($mobile, $allowedTestNumbers)) {
                    return ['type' => 'success'];
                } else {
                    return ['type' => 'failed', 'message' => 'Otp invalid or expired'];
                }
            }

            // if ($otp == '8712') {
            //     return [
            //         'type' => 'success'
            //     ];
            // }

            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('msg91.verify_url'), [
                'mobile' => sprintf('%s%s', $country_code, $mobile),
                'authkey' => config('msg91.auth_key'),
                'otp' => $otp,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::critical($e->getMessage(), ['response' => $response]);
            throw $e;
        }
    }

    public function otpResend(string $country_code, int $mobile)
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->get(config('msg91.resend_url'), [
                'mobile' => sprintf('%s%s', $country_code, $mobile),
                'authkey' => config('msg91.auth_key'),
            ]);
            return $response->json();
        } catch (\Exception $e) {
            Log::critical($e->getMessage(), ['response' => $response]);
            throw $e;
        }
    }
}
