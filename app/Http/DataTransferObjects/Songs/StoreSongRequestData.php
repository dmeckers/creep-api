<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class StoreSongRequestData extends Data
{
    public function __construct(

        public UploadedFile $file,

        #[Unique('songs', 'code')]
        public string $code,

        public ?string $name = 'Unknown Track',

        #[Exists('artists', 'id')]
        public ?int $artistId = null,

        public ?int $ownerId = null,

        #[Exists('playlists', 'id')]
        public ?int $playlistId = null,
    ) {
    }
}
