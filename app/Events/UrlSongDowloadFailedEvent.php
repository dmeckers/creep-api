<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UrlSongDowloadFailedEvent implements ShouldBroadcast
{

    public function __construct(
        private readonly string $url,
        private readonly string $errorMessage,
        private readonly int $userId
    ) {
    }

    public function broadcastWith(): array
    {
        return [
            'url' => $this->url,
            'error_message' => $this->errorMessage,
            // TODO pass as constructor when other download options are implemented
            'provider' => 'youtube',
        ];
    }

    public function broadcastAs(): string
    {
        return 'url.song.download.failed';
    }

    public function broadcastOn()
    {
        return ["user.{$this->userId}.upload-progress"];
    }
}
