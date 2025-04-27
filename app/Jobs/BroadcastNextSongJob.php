<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\TrackStartedEvent;
use App\Models\Station\Station;
use App\Models\TrackBroadcast;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastNextSongJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    public function __construct(
        private readonly TrackBroadcast $trackBroadcast
    ) {
    }

    public function handle(): void
    {
        $station = $this->trackBroadcast->relatedStation();

        if (!$station || $station->isLive() === false) {
            return;
        }

        broadcast(
            new TrackStartedEvent($this->trackBroadcast)
        );

        $this->trackBroadcast->update([
            TrackBroadcast::BROADCASTED_AT => now(),
        ]);

        if ($station->isLooped()) {
            $this->scheduleNext($station);
        }
    }

    public function scheduleNext(Station $station): void
    {
        /**
         * @var TrackBroadcast|null $next
         */
        $next = $this->getNext($station);

        \Log::info('Scheduling next song', [
            'song' => $this->trackBroadcast->relatedSong()->getId(),
        ]);

        if ($next === null) {
            $next = $this->startOver($station);
        }

        $delay = (int) now()->diffInSeconds($next->getStartAt(), false);

        dispatch(new BroadcastNextSongJob($next))->delay(
            max(0, $delay)
        );
    }

    public function getNext(Station $station): ?TrackBroadcast
    {
        $queue = $station->relatedStationQueue();

        return $queue?->trackBroadcasts()
            ->where('order', '>', $this->trackBroadcast->getOrder())
            ->where('start_at', '>', $this->trackBroadcast->getStartAt())
            ->orderBy('order')
            ->first();
    }

    public function startOver(Station $station): TrackBroadcast
    {
        $queue = $station->relatedStationQueue();

        $next = $queue?->trackBroadcasts()
            ->where('order', '<>', $this->trackBroadcast->getOrder())
            ->orderBy('order')
            ->firstOrFail();

        $next->update([
            TrackBroadcast::START_AT => now()->addSeconds(
                (int) $this->trackBroadcast->relatedSong()->getDuration()
            ),
        ]);

        return $next;
    }
}
