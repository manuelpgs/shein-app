<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Ensure2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->two_fa_enabled) {
            $tokenName = $request->bearerToken() ? $request->user()->currentAccessToken()->name : null;
        
            if ($tokenName !== 'auth_token_2fa') {
                return response()->json(['message' => '2FA verification required'], 403);
            }
        }

        return $next($request);
    }
}
