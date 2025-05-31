<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use App\Models\Playlist;
use App\Models\User;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Regex;
use Spatie\LaravelData\Data;

class UploadSongFromYoutubeRequestData extends Data
{
    public function __construct(
        public string $youtubeUrl,
        #[Exists(User::TABLE_NAME, User::ID)]
        public ?int $userId,
        #[Exists(Playlist::TABLE_NAME, Playlist::ID)]
        public ?int $playlistId = null,
    ) {
    }
}
