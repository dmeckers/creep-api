<?php

declare(strict_types=1);

namespace App\Http\DataTransferObjects\Songs;

use App\Models\Playlist;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Data;

class GetSongsRequestData extends Data
{
    public function __construct(
        public int $page = 1,
        public int $per_page = 10,
        public ?string $search = null,
        public string $sort_by = 'created_at',
        public string $sort_order = 'desc',
        public ?string $name = null,
        #[Exists(Playlist::TABLE_NAME, Playlist::ID)]
        public ?int $exclude_playlist_id = null,
    ) {
    }
}
