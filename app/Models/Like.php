<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'like_count',
    ];

    protected $casts = [
        'like_count' => 'integer',
    ];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
