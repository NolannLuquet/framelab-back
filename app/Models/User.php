<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['lastname', 'firstname', 'username', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function votes(): HasMany {
        return $this->hasMany(Vote::class);
    }

    public function reports(): HasMany {
        return $this->hasMany(Report::class);
    }
}
