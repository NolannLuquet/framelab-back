<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function store(Request $request)
    {
        if (User::where('email', $request->json('email'))->exists()) {
            return response()->json([
                'data' => null,
                'error' => [
                    'type' => 'https://framelab.com/conflict-error',
                    'title' => 'Email déjà utilisé.',
                    'status' => 409,
                    'detail' => 'Un compte existe déjà avec ce mail',
                ],
                'meta' => null,
            ], 409);
        }

        if (User::where('username', $request->json('username'))->exists()) {
            return response()->json([
                'data' => null,
                'error' => [
                    'type' => 'https://framelab.com/conflict-error',
                    'title' => 'Pseudo déjà utilisé.',
                    'status' => 409,
                    'detail' => 'Un compte existe déjà avec ce pseudo',
                ],
                'meta' => null,
            ], 409);
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
            return response()->json([
                'data' => null,
                'error' => [
                    'type' => 'https://framelab.com/error',
                    'title' => 'Email ou mot de passe incorrect',
                    'status' => 401,
                    'detail' => 'Vérifiez votre email et votre mot de passe',
                ],
                'meta' => null,
            ], 401);
        }

        if (!$user->verified) {
            return response()->json([
                'data' => null,
                'error' => [
                    'type' => 'https://framelab.com/error',
                    'title' => 'Compte non validé.',
                    'status' => 403,
                    'detail' => 'Cliquez sur le lien de validation avant de vous connecter.',
                ],
                'meta' => null,
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

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
}
