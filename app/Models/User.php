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

    public function participations(): HasMany
    {
        return $this->hasMany(Participation::class);
    }
}
