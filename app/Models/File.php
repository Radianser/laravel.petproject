<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fileable_id',
        'fileable_type',
        'src',
        'title',
        'original_title',
        'size',
        'content_type',
        'extension'
    ];

    protected $casts = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}