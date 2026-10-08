<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    protected $fillable = ['content', 'comment_date', 'user_id', 'participation_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participation(): BelongsTo
    {
        return $this->belongsTo(Participation::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
