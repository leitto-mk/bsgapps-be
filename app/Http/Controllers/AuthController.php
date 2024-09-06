<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\JWTService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $user, $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->user = new User;
        $this->jwtService = $jwtService;
    }

    public function authentication(Request $request)
    {
        $set = env('LOGIN_PASSWORD_SET');
        $def = env('LOGIN_PASSWORD_DEF');

        $formData = $request->only('username', 'password');
        $validator = [
            'username' => 'required',
            'password' => 'required'
        ];

        /** Validate Request */
        $validator = Validator::make($formData, $validator);
        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), TRUE);
            $errorsMessage = [];

            foreach ($errors as $k => $v) {
                $errorsMessage[] = $v[0];
            }

            return response()->json([
                'status' => FALSE,
                'message' => $errorsMessage
            ], 422);
        }

        $username = $formData['username'];
        $password = md5($def . $set . md5($formData['password']) . $set . $def . $set . $def);

        /** Validate User */
        if (!$this->user->userValidate($username)) {
            return response()->json([
                'status' => FALSE,
                'message' => 'User tidak tersedia, silahkan hubungi admin.'
            ], 401);
        }

        /** Authentication */
        $auth = $this->user->authentication($username, $password);
        if (!$auth) {
            return response()->json([
                'status' => FALSE,
                'message' => 'Username atau Password salah.'
            ], 401);
        }

        $expiresIn = 3600;
        $payload = json_decode($auth, TRUE);

        $token = $this->jwtService->encode($payload, $expiresIn);

        return response()->json([
            'type' => 'bearer',
            'access_token' => $token,
            'expires_in' => $expiresIn
        ], 200);
    }
}
