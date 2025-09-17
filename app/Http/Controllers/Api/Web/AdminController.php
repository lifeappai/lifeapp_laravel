<?php

namespace App\Http\Controllers\Api\Web;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\Web\AdminLoginRequest;
use App\Http\Requests\Auth\WEB\AdminRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::select('id as admin_id', 'password as admin_pass', 'email', 'type')
            ->where(['email' => $data['email'], 'type' => UserType::Admin])
            ->first();

        if (!$user) {
            $msg = [
                'errors' => [
                    'title' => 'Login Failed!',
                    'icon' => 'AlertCircleIcon',
                    'type' => 'warning',
                    'message' => 'Admin not exist'
                ]
            ];
        } else {
            
            if ($request['password'] === $user->admin_pass) {
                $admin = Auth::loginUsingId($user->admin_id);
                $admin->id = $user->admin_id;
                $admin->role = strtolower(UserType::getKey($admin->type));
                $token = $admin->createToken('Life App Admin')->accessToken;
                $admin->ability = [['action' => 'manage', 'subject' => 'all']];

                $msg = [
                    'accessToken' => $token,
                    'message' => 'Login Success Full',
                    'res' => true,
                    'admin' => $admin,
                ];
            } else {
                $msg = [
                    'errors' => [
                        'title' => 'Login Failed!',
                        'icon' => 'EyeOffIcon',
                        'type' => 'warning',
                        'message' => 'Admin wrong password'
                    ]
                ];
            }
        }

        return new JsonResponse($msg, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdminRegisterRequest $request
     * @return JsonResponse
     */
    public function store(AdminRegisterRequest $request): JsonResponse
    {
        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type = UserType::Admin;
        $user->save();
        $user->accessToken = $user->createToken('LifeApp Admin')->accessToken;
        return new JsonResponse(['data' => $user], Response::HTTP_OK);
    }


}
