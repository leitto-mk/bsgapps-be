<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentTypeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the Content-Type header is application/json
        if (!$request->isJson() && $request->method() != "GET") {
            return response()->json([
                'error' => 'Invalid Content-Type',
                'message' => 'Only JSON requests are accepted.'
            ], Response::HTTP_UNSUPPORTED_MEDIA_TYPE); // 415 Unsupported Media Type
        }

        return $next($request);
    }
}
