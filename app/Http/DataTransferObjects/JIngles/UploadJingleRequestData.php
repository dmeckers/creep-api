<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Jingles;

use App\Models\Jingle;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class UploadJingleRequestData extends Data
{
    public function __construct(
        public UploadedFile $file,
        #[Unique(table: 'jingles', column: Jingle::CODE)]
        public string $code,
        #[Exists('stations', 'id')]
        public int $station_id,
    ) {
    }
}
