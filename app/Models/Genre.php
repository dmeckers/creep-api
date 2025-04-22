<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function stations(): MorphToMany
    {
        return $this->morphedByMany(Station::class, 'genreable', 'genreables');
    }

    public function songs(): MorphToMany
    {
        return $this->morphedByMany(Song::class, 'genreable', 'genreables');
    }

    public function artists(): MorphToMany
    {
        return $this->morphedByMany(Artist::class, 'genreable', 'genreables');
    }
}
