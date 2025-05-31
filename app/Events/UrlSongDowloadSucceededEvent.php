<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UrlSongDowloadSucceededEvent implements ShouldBroadcast
{

    public function __construct(
        private readonly string $url,
        private readonly int $userId
    ) {
    }

    public function broadcastWith(): array
    {
        return [
            'url' => $this->url,
            // TODO pass as constructor when other download options are implemented
            'provider' => 'youtube',
        ];
    }

    public function broadcastAs(): string
    {
        return 'url.song.download.succeeded';
    }

    public function broadcastOn()
    {
        return ["user.{$this->userId}.upload-progress"];
    }
}
