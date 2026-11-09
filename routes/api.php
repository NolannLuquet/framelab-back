<?php

use App\Http\Controllers\Api\ChallengeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'store']);
Route::post('/session', [UserController::class, 'login']);
Route::get('/users/me', [UserController::class, 'me'])->middleware('auth:sanctum');
Route::get('/validate', [UserController::class, 'validate']);
Route::delete('/session', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/challenges/current', [ChallengeController::class, 'current'])->middleware('auth:sanctum');
Route::get('/challenges', [ChallengeController::class, 'index'])->middleware('auth:sanctum');
Route::get('/challenges/{id}', [ChallengeController::class, 'show'])->middleware('auth:sanctum');

Route::post('/challenges', [ChallengeController::class, 'store'])->middleware('auth:sanctum');
Route::patch('/challenges/{id}', [ChallengeController::class, 'update'])->middleware('auth:sanctum');
