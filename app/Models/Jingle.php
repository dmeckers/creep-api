<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jingle extends Model
{
    public const TABLE = 'jingles';

    public const CODE = 'jingle_code';
    public const STATION_ID = 'station_id';

    protected $fillable = [
        self::CODE,
        self::STATION_ID,
    ];

    protected $table = self::TABLE;

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function getCode(): string
    {
        return $this->getAttribute(self::CODE);
    }

    public function getStationId(): int
    {
        return $this->getAttribute(self::STATION_ID);
    }
}
