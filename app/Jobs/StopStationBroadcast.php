<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\StationBroadcastHasStoppedEvent;
use App\Models\Station\Station;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StopStationBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Station $station,
    ) {
    }

    public function handle(): void
    {
        $this->station->stationQueue()->delete();

        event(new StationBroadcastHasStoppedEvent($this->station));
    }
}
