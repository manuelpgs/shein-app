<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use PragmaRX\Google2FALaravel\Facade as Google2FA;


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
    
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            // El código 2FA es válido, generamos el token de acceso
            // $token = $user->createToken('auth_token')->plainTextToken;
            $token = $user->createToken('auth_token_2fa')->plainTextToken;
            return response()->json([
                'token' => $token,
                'user' => $user->only(['id', 'name', 'email'])
            ]);
        } else {
            return response()->json(['message' => 'Invalid 2FA code'], 401);
        }
    }    

    public function generate2faSecret(Request $request)
    {
        $user = $request->user();
        $google2fa = app('pragmarx.google2fa');
    
        $secret = $google2fa->generateSecretKey();
    
        $otpUrl = $google2fa->getQRCodeUrl(
            config('app.name'), // Issuer (e.g. MyApp)
            $user->email,       // Label (user's email or name)
            $secret             // The 2FA secret
        );
    
        return response()->json([
            'secret' => $secret,
            'otpauth_url' => $otpUrl,
        ]);
    }

    public function enable2fa(Request $request)
    {
        $google2fa = app('pragmarx.google2fa');
        $user = $request->user();
    
        $secret = $request->input('secret');
        $code = $request->input('code');
    
        if (!$secret || !$code) {
            return response()->json(['message' => 'Missing 2FA secret or code'], 422);
        }
    
        $valid = $google2fa->verifyKey($secret, $code);
    
        if ($valid) {
            $user->google2fa_secret = $secret;
            $user->two_fa_enabled = true;
            $user->save();
    
            return response()->json(['message' => '2FA enabled successfully']);
        }
    
        return response()->json(['message' => 'Invalid 2FA code'], 403);
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'code' => 'required|string',
        ]);
    
        $user = User::findOrFail($request->user_id);
    
        $google2fa = new Google2FA();
    
        if ($google2fa->verifyKey($user->google2fa_secret, $request->code)) {
            // Create the token now that 2FA is passed
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'message' => '2FA verified successfully',
                'token' => $token
            ]);
        }
    
        return response()->json(['message' => 'Invalid 2FA code'], 401);
    }
}