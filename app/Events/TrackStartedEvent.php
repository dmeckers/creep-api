<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\TrackBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\InteractsWithQueue;

class TrackStartedEvent implements ShouldBroadcast
{
    use InteractsWithQueue, InteractsWithSockets;

    public function __construct(private readonly TrackBroadcast $trackBroadcast)
    {
    }

    public function broadcastWith(): array
    {
        $song = $this->trackBroadcast->relatedSong();

        $broadcastWith = [
            'track' => [
                'id' => $song->getId(),
                'code' => $song->getCode(),
                'start_at' => $this->trackBroadcast->getBroadcastedAt()?->toISOString(),
                'duration' => $song->getDuration(),
                'now' => now()->toISOString(),
            ]
        ];

        \Log::info('Broadcasting track started event', [
            'track' => $broadcastWith,
        ]);

        return $broadcastWith;
    }

    public function broadcastAs(): string
    {
        return 'track.started';
    }

    public function broadcastOn(): array
    {
        return ['station.' . $this->trackBroadcast->relatedStation()?->getMountPoint()];
    }
}
