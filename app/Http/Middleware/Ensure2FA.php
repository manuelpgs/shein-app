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
            $token = $user->currentAccessToken();
    
            if (!$token || $token->name !== 'auth_token_2fa') {
                return response()->json(['message' => '2FA verification required'], 403);
            }
        }
    
        return $next($request);
    }
    
}
