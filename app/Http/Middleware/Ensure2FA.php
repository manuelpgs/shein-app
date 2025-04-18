<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Laravel\Facade as Google2FA;

class Ensure2FA
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->google2fa_secret && !$request->session()->get('2fa_passed')) {
            return response()->json(['message' => '2FA required'], 403);
        }

        return $next($request);
    }
}