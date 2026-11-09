<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function current()
    {
        $challenge = Challenge::where('status', 'active')->firstOrFail();

        return response()->json([
            'data' => $challenge,
            'error' => null,
            'meta' => null,
        ], 200);
    }

    public function index()
    {
        $challenges = Challenge::where('status', '!=', 'draft')
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'data' => $challenges,
            'error' => null,
            'meta' => null,
        ], 200);
    }

    public function show($id)
    {
        $challenge = Challenge::where('status', '!=', 'draft')->findOrFail($id);

        return response()->json([
            'data' => $challenge,
            'error' => null,
            'meta' => null,
        ], 200);
    }
    public function store(Request $request)
    {
        if ($request->user()->role != 'admin') {
            throw new AuthorizationException('Réservé aux administrateurs.');
        }

        $request->validate([
            'theme' => 'required',
            'description' => 'required',
            'category' => 'required',
            'base_photo' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $challenge = $request->user()->challenges()->create([
            'theme' => $request->json('theme'),
            'description' => $request->json('description'),
            'category' => $request->json('category'),
            'base_photo' => $request->json('base_photo'),
            'start_date' => $request->json('start_date'),
            'end_date' => $request->json('end_date'),
            'status' => 'draft',
        ]);

        return response()->json([
            'data' => $challenge,
            'error' => null,
            'meta' => null,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        if ($request->user()->role != 'admin') {
            throw new AuthorizationException('Réservé aux administrateurs.');
        }

        $challenge = Challenge::findOrFail($id);

        $request->validate([
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:draft,active,closed',
        ]);

        if ($request->json('status') == 'active') {
            Challenge::where('status', 'active')
                ->where('id', '!=', $challenge->id)
                ->update(['status' => 'closed']);
        }

        $challenge->update($request->all());

        return response()->json([
            'data' => $challenge,
            'error' => null,
            'meta' => null,
        ], 200);
    }
}
