<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends ResponseController
{
    public function adminLogin(Request $request)
    {
        try {
            $validate = array(
                'email' => 'required',
                "password" => 'required',
            );
            $validator = Validator::make($request->all(), $validate);
            if ($validator->fails()) {
                return $this->sendError($validator->errors()->first());
            }

            $admin = User::where('email', $request->email)->where('type', UserType::Admin)->first();
            if (!$admin) {
                return $this->sendError("Email Not Exist");
            }
            if (!Hash::check($request->password, $admin->password)) {
                return $this->sendError('Password Does Not Match');
            }
            $token = $admin->createToken('AppAdminToken')->accessToken;
            $response = [];
            $response['token'] = (string)$token;
            return $this->sendResponse($response, "Login Success");
        } catch (\Exception $exception) {
            return response()->json([
                'error' => $exception->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
