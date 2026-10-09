<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function store(Request $request)
    {
        if (User::where('email', $request->json('email'))->exists()) {
            throw ValidationException::withMessages(['email' => 'Cet email est déjà utilisé.']);
        }

        if (User::where('username', $request->json('username'))->exists()) {
            throw ValidationException::withMessages(['username' => 'Ce pseudo est déjà utilisé.']);
        }

        $user = new User();
        $user->lastname = $request->json('lastname');
        $user->firstname = $request->json('firstname');
        $user->username = $request->json('username');
        $user->email = $request->json('email');
        $user->password = Hash::make($request->json('password'));
        $user->save();

        $verification_token = $user->createToken('verification_token');
        $url = url('/api/validate?token=' . urlencode($verification_token->plainTextToken));

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'username' => $user->username,
                ],
                'validation_url' => $url,
            ],
            'error' => null,
            'meta' => null,
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->json('email'))->first();

        if (!$user || !Hash::check($request->json('password'), $user->password)) {
            throw new AuthenticationException('Email ou mot de passe incorrect.');
        }

        if (!$user->verified) {
            throw new AuthorizationException('Compte non validé.');
        }

        $token = $user->createToken('auth_token', ['*'], now()->plus(years: 1))->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                ],
            ],
            'error' => null,
            'meta' => null,
        ], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'username' => $user->username,
                'role' => $user->role,
            ],
            'error' => null,
            'meta' => null,
        ], 200);
    }

    public function validate(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->query('token'));

        if (!$token || $token->name != 'verification_token') {
            throw new AuthenticationException('Lien de validation invalide.');
        }

        $user = $token->tokenable;
        $user->verified = true;
        $user->email_verified_at = now();
        $user->save();

        $token->delete();

        return response()->json([
            'data' => [
                'message' => 'Compte validé, vous pouvez vous connecter.',
            ],
            'error' => null,
            'meta' => null,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'data' => [
                'message' => 'Déconnexion réussie.',
            ],
            'error' => null,
            'meta' => null,
        ], 200);
    }
}
