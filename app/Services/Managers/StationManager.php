<?php

declare(strict_types=1);

namespace App\Services\Managers;

use App\Models\Station;
use Exception;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Str;

class StationManager
{
    private const LIQUIDSOAP_CONFIG_PATH           = '/var/liq/example/';
    private const LIQUIDSOAP_CONTAINER_CONFIG_PATH = '/etc/liquidsoap/script.liq';

    public function __construct(
        private readonly PendingProcess $process
    ) {
    }

    public function spinUpStation(Station $station, int $playlistId = null)
    {
        $this->checkIfCanSpinUpStation($station);

        $station->setPlayingPlaylist(
            $playlistId
            ? $station->playlists()->find($playlistId)
            : $station->playlists()->first()
        );

        $fileName = $this->prepareStationConfig($station);

        $result = $this->process->run(
            [
                'docker',
                'run',
                '-d',
                '--rm',
                '--name',
                Str::slug($station->getName()),
                '--platform',
                'linux/amd64',
                '--network',
                config('liquidsoap.docker.shared-network'),
                '--volume',
                config('liquidsoap.docker.host-config-mount-path') . '/' . $fileName . ':' . self::LIQUIDSOAP_CONTAINER_CONFIG_PATH,
                '--memory',
                config('liquidsoap.docker.memory-limit'),
                config('liquidsoap.docker.image'),
                // 'pltnk/liquidsoap:latest',
                // self::LIQUIDSOAP_CONTAINER_CONFIG_PATH
                'tail',
                '-f',
                '/dev/null'
            ]
        );
        // eval $(opam env) && liquidsoap /etc/liquidsoap/script.liq

        if ($result->failed()) {
            throw new Exception('Failed to spin up station: ' . $result->errorOutput());
        }

        $hash = $result->output();

        $logs = $this->process->run(
            [
                'docker',
                'logs',
                '--follow',
                $hash
            ]
        );

        dump($logs->output());

        $station->setIsLive(true);

        $station->save();
    }

    public function spinDownStation(Station $station)
    {
        if (!$station->isLive()) {
            throw new Exception('Station is not live');
        }

        $result = $this->process->run(
            [
                'docker',
                'stop',
                $station->getName()
            ]
        );

        if ($result->failed()) {
            throw new Exception('Failed to spin down station: ' . $result->errorOutput());
        }

        $station->setIsLive(false);

        $station->save();
    }

    private function checkIfCanSpinUpStation(Station $station)
    {
        if ($station->isLive()) {
            throw new Exception('Station is already live');
        }

        if ($station->playlists()->count() === 0) {
            throw new Exception('Station has no playlists');
        }

        if ($station->playlists()->whereHas('songs')->count() === 0) {
            throw new Exception('Station has no songs');
        }
    }

    private function prepareStationConfig(Station $station)
    {
        $configPath = self::LIQUIDSOAP_CONFIG_PATH; // Station name is unique in db

        if (!is_dir($configPath)) {
            mkdir($configPath, 0755, true);
        }

        $fileName = Str::slug($station->getMountPoint()) . '.liq';

        file_put_contents(
            $configPath . $fileName,
            str_replace(
                [
                    '{{ mount_point }}',
                    '{{ station_name }}',
                    '{{ station_description }}',
                    '{{ station_id }}',
                    '{{ api_url }}',
                ],
                [
                    $station->getMountPoint(),
                    $station->getName(),
                    $station->getDescription() ?? 'No description',
                    $station->getId(),
                    'http://api:8000'
                ],
                file_get_contents(self::LIQUIDSOAP_CONFIG_PATH . 'template.liq')
            )
        );

        return $fileName;
    }
}
