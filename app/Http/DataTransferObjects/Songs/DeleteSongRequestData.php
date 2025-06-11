<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class DeleteSongRequestData extends Data
{
    public function __construct(
        #[Exists('songs', 'id')]
        public int $id,
    ) {
    }
}
