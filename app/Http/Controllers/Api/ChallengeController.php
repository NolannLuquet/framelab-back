<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;

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
}
