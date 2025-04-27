<?php

declare(strict_types=1);

namespace App\Models\Station;

use App\Models\Station\StationSchema;
use App\Models\User;
use Laravel\Scout\Searchable;

class StationIndex extends StationSchema
{
    public const INDEX_NAME = 'stations';

    public const OWNER = 'owner';

    use Searchable;

    public function searchableAs(): string
    {
        return self::INDEX_NAME;
    }

    public function toSearchableArray(): array
    {
        return [
            self::ID => $this->getId(),
            self::NAME => $this->getName(),
            self::IS_LIVE => $this->isLive(),
            self::MOUNT_POINT => $this->getMountPoint(),
            self::OWNER => [
                User::ID => $this->relatedOwner()->getId(),
                User::NAME => $this->relatedOwner()->getName(),
            ],
            self::IS_PUBLIC => $this->getIsPublic(),
            self::DESCRIPTION => $this->getDescription(),
        ];
    }

    public function shouldBeSearchable(): bool
    {
        // return $this->isLive() && $this->getIsPublic();
        return true;
    }
}
