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
