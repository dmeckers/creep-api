<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Station\Station;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\InteractsWithQueue;

class StationBroadcastHasStoppedEvent implements ShouldBroadcast
{
    use InteractsWithQueue, InteractsWithSockets;

    public function __construct(private readonly Station $station)
    {
    }

    public function broadcastWith(): array
    {
        return [
            'station' => [
                'id' => $this->station->getId(),
                'mount_point' => $this->station->getMountPoint(),
                'name' => $this->station->getName(),
            ],
        ];
    }

    public function broadcastAs(): string
    {
        return 'station.down';
    }

    public function broadcastOn(): array
    {
        return ['station.' . $this->station->getMountPoint()];
    }
}
