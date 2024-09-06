<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JWTService;

class JWTMiddleware
{
    protected $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** JWT Auth */
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Token empty.'
                ],
                401
            );
        }

        $validate = $this->jwtService->validate($token);
        if ($validate) {
            return response()->json($validate['data'], $validate['http_status']);
        }

        return $next($request);
    }
}
