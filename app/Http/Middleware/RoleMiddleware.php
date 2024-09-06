<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JWTService;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
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
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roleAdmin, $roleIt, $rolePosName)
    {
        $token = $request->bearerToken();
        $payload = $this->jwtService->decode($token)['data'];

        $request->merge([
            'token_payload' => $payload
        ]);

        /** Auth Role Admin User || IT User || Pos Name User */
        if (
            ($roleAdmin != NULL && $payload->is_admin != $roleAdmin) ||
            ($roleIt != NULL && $payload->id_role_it != $roleIt) ||
            ($rolePosName != NULL && $payload->pos_name != $rolePosName)
        ) {
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'You do not have the required permissions to access this resource.'
                ],
                403
            );
        }

        return $next($request);
    }
}
