<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'owner_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function genres(): MorphMany
    {
        return $this->morphMany(Genre::class, 'genreable', 'genreables');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function like(): MorphOne
    {
        return $this->morphOne(Like::class, 'likeable');
    }
}
