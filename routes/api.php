<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Participation;
use Illuminate\Support\Facades\Hash;
use App\Models\Tag;
use App\Models\Comment;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return Challenge::with('user', 'participations')->get();
});

Route::get('/test-user', function () {
    return User::create([
        'lastname' => 'Dupont',
        'firstname' => 'Jean',
        'username' => 'jdupont',
        'email' => 'jean@gmail.com',
        'password' => Hash::make('secret123'),
    ]);
});

Route::get('/test-challenge', function () {
    return Challenge::create([
        'theme' => 'Nature',
        'description' => 'Photographiez la nature',
        'category' => 'Paysage',
        'base_photo' => 'nature.jpg',
        'start_date' => '2026-10-01',
        'end_date' => '2026-10-08',
        'user_id' => 1,
    ]);
});

Route::get('/test-participation', function () {
    return Participation::create([
        'photo' => 'photo.jpg',
        'description' => 'Ma photo de lapin',
        'submission_date' => '2026-10-03',
        'user_id' => 1,
        'challenge_id' => 1,
    ]);
});

Route::get('/test-mes-participations', function () {
    return User::with('participations')->get();
});

Route::get('/test-tag', function () {
    return Tag::create(['label' => 'nature']);
});

Route::get('/test-associate', function () {
    Participation::find(1)->tags()->attach(1);

    return Participation::with('tags')->find(1);
});

Route::get('/test-comment', function () {
    return Comment::create([
        'content' => 'Super photo !',
        'comment_date' => '2026-10-04',
        'user_id' => 1,
        'participation_id' => 1,
    ]);
});

Route::get('/test-comments', function () {
    return Participation::with('comments')->find(1);
});
