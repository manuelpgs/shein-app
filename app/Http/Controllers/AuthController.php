<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google2FA;


class AuthController extends Controller
{
    public function loginWithout2fa(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            if ($user->two_fa_enabled) {
                // El 2FA está habilitado, no generamos el token de acceso ahora
                return response()->json(['two_fa_required' => true, 'user_id' => $user->id]);
            } else {
                // El 2FA no está habilitado, generamos el token de acceso
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json(['token' => $token]);
            }
        }
    
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function verify2faLogin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string|min:6|max:6', // Asumiendo códigos de 6 dígitos
        ]);
    
        $user = User::findOrFail($request->user_id);
    
        if (! $user->two_fa_enabled || !$user->google2fa_secret) {
            return response()->json(['message' => '2FA is not enabled for this user'], 400);
        }
    
        if (Google2FA::verifyKey($user->google2fa_secret, $request->code)) {
            // El código 2FA es válido, generamos el token de acceso
            $token = $user->createToken('auth_token')->plainTextToken;
            // Limpiamos la sesión de 2FA si la usamos
            $request->session()->forget('2fa_passed_' . $user->id);
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['message' => 'Invalid 2FA code'], 401);
        }
    }    

    public function generate2faSecret(Request $request)
    {
        $user = $request->user();
        $secret = Google2FA::generateSecretKey();

        $user->google2fa_secret = $secret;
        $user->save();

        return response()->json(['secret' => $secret]);
    }

    public function enable2fa(Request $request)
    {
        $user = $request->user();
        $secret = $request->input('secret');
        $code = $request->input('code');

        if (Google2FA::verifyKey($secret, $code)) {
            $user->google2fa_secret = $secret;
            $user->two_fa_enabled = true;
            $user->save();

            return response()->json(['message' => '2FA enabled']);
        }

        return response()->json(['message' => 'Invalid verification code'], 400);
    }

    public function disable2fa(Request $request)
    {
        $user = $request->user();

        $user->google2fa_secret = null;
        $user->two_fa_enabled = false;
        $user->save();

        return response()->json(['message' => '2FA disabled']);
    }

    public function verify2fa(Request $request)
    {
        $user = $request->user();
        $code = $request->input('code');

        if (Google2FA::verifyKey($user->google2fa_secret, $code)) {
            $request->session()->put('2fa_passed', true);

            return response()->json(['message' => '2FA verified']);
        }

        return response()->json(['message' => 'Invalid verification code'], 400);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}