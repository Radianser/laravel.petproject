<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;
use App\Models\Like;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'imageable_id',
        'imageable_type',
        'src',
        'home_page'
    ];

    protected $casts = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
