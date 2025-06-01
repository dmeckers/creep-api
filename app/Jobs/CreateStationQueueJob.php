<?php

namespace App\Jobs;

use App\Events\StationStartedEvent;
use App\Models\Song;
use App\Models\Station\Station;
use App\Models\StationQueue;
use App\Models\TrackBroadcast;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateStationQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly Station $station)
    {
    }

    public function handle(): void
    {
        $hasQueue = $this->station->has(Station::STATION_QUEUE_RELATION)->exists();

        if ($hasQueue) {
            $this->station->stationQueue()->delete();
        }

        /**
         * @var StationQueue $stationQueue
         */
        $stationQueue = $this->station->stationQueue()->create();

        $startAt = Carbon::now()->addSeconds(2);
        $order = 0;
        $offset = 0;

        $this->station->playingPlaylist()->songs()->each(function (Song $song) use ($stationQueue, $startAt, &$offset, &$order) {
            $scheduledAt = $startAt->copy()->addSeconds($offset);

            $stationQueue->trackBroadcasts()->create([
                TrackBroadcast::SONG_ID => $song->id,
                TrackBroadcast::START_AT => $scheduledAt,
                TrackBroadcast::ORDER => ++$order,
            ]);

            $offset += $song->duration;
        });

        $first = $stationQueue->trackBroadcasts()->first();

        $delay = $first->getStartAt()->diffInSeconds(date: now(), absolute: false);

        if ($delay < 0) {
            $delay = 0;
        }

        $this->station->update([Station::IS_LIVE => true]);
        
        event(new StationStartedEvent($this->station));

        BroadcastNextSongJob::dispatch($first)->delay($delay);
    }
}
