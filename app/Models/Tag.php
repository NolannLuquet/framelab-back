<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['label'];

    public function participations(): BelongsToMany
    {
        return $this->belongsToMany(Participation::class, 'associate');
    }
}
