<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'linkable_id',
        'linkable_type',
        'domain',
        'uri',
        'title'
    ];

    protected $casts = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
}
