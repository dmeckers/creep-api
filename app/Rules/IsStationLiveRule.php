<?php

namespace App\Rules;

use App\Services\Repositories\Stations\StationRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsStationLiveRule implements ValidationRule
{

    public function __construct(private readonly StationRepository $repo)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_int($value) === true || intval($value) > 0) {
            $station = $this->repo->findOrFail($value);

            if ($station->isLive() === true) {
                return;
            }

            $fail('The station is not live.');
        }

        if (is_string($value) === true) {
            $station = $this->repo->findByMountPoint($value);

            if ($station === null) {
                $fail('The station does not exist.');
                return;
            }

            if ($station->isLive() === true) {
                return;
            }

            $fail('The station is not live.');

            return;
        }

        $fail('The station must be live.');
    }
}
