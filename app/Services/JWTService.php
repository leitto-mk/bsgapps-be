<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Log;

class JWTService
{
    protected $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET_KEY');
    }

    public function encode(array $payload, $expiresIn = 3600, $alg = 'HS256')
    {
        $payload['iat'] = time(); // Issuet at time
        $payload['exp'] = time() + $expiresIn; // Set expiration time

        return JWT::encode($payload, $this->secret, $alg);
    }

    public function decode($token, $alg = 'HS256')
    {
        try {
            return [
                'http_status' => 200,
                'data' => JWT::decode($token, new Key($this->secret, $alg))
            ];
        } catch (ExpiredException $e) {
            $message = $e->getMessage();
            // Log or handle the expired token error
            return [
                'http_status' => 401,
                'data' => [
                    'status' => false,
                    'message' => $message
                ]
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            // Log or handle the expired token error
            return [
                'http_status' => 401,
                'data' => [
                    'status' => false,
                    'message' => $message
                ]
            ];
        }
    }

    public function validate($token)
    {
        $decoded = $this->decode($token, 'HS256');

        if ($decoded['http_status'] != 200) {
            return $decoded;
        }

        return FALSE;
    }
}
