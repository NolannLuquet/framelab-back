<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    protected $fillable = ['creativity', 'technique', 'theme_respect', 'vote_date', 'user_id', 'participation_id'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participation(): BelongsTo {
        return $this->belongsTo(Participation::class);
    }
}
