<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\TrackStartedEvent;
use App\Models\Station\Station;
use App\Models\TrackBroadcast;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BroadcastNextSong extends Command
{
    protected $signature = 'station:broadcast-next-song';

    protected $description = 'Run broadcast next song';

    public function handle()
    {
        Station::where(Station::IS_LIVE, '=', true)->each(
            function (Station $station) {

                /**
                 * @var TrackBroadcast|null $next
                 */
                $next = $station->relatedStationQueue()
                        ?->trackBroadcasts()
                    ->whereDate(TrackBroadcast::START_AT, '>=', now())
                    ->orderBy(TrackBroadcast::ORDER)
                    ->first();

                if (!$next || false === now()->greaterThanOrEqualTo($next->getStartAt())) {
                    return;
                }

                \Log::info('Broadcasting next song', [
                    'song' => $next->relatedSong()->getId(),
                    'song2' => $next,
                    'station' => $station->getId(),
                ]);

                broadcast(new TrackStartedEvent($next));

                $next->update([
                    TrackBroadcast::BROADCASTED_AT => now(),
                ]);

                $this->scheduleNext($next, $station);
            }
        );
    }

    public function scheduleNext(TrackBroadcast $broadcast, Station $station): void
    {
        if ($station->isLooped() === false) {
            return;
        }

        $stationQueue = $station->relatedStationQueue();

        $lastIdx = $stationQueue?->trackBroadcasts()->max('order');

        $song = $broadcast->getOrder() === $lastIdx
            ? $stationQueue->trackBroadcasts()->first()
            : $stationQueue->trackBroadcasts()
                ->where(TrackBroadcast::ORDER, '>', $broadcast->getOrder())
                ->orderBy(TrackBroadcast::ORDER)
                ->first();

        $song?->update([
            TrackBroadcast::START_AT => now()->addSeconds(
                (int) $song->relatedSong()->getDuration()
            ),
        ]);
    }
}
