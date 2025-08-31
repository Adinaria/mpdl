<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UuidMiddleware
{
    public function handle(Request $request, Closure $next, $paramName = 'uuid')
    {
        $uuid = $request->route($paramName);

        if (!Str::isUuid($uuid)) {
            return response()->json([
                'message' => 'Invalid UUID format'
            ], 400);
        }
        return $next($request);
    }
}
