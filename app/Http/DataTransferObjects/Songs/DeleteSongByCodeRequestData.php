<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class DeleteSongByCodeRequestData extends Data
{
    public function __construct(
        #[Exists('songs', 'code')]
        public string $code,
    ) {
    }
}
