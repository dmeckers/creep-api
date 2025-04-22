<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Station>
 */
class StationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Station::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'owner_id' => \App\Models\User::factory(),
            'is_public' => fake()->boolean(),
            'is_live' => fake()->boolean(),
            'mount_point' => fake()->unique()->word(),
            'stream_url' => fake()->unique()->url(),
            'stream_type' => 'audio/mpeg',
            'stream_format' => 'mp3',
            'stream_bitrate' => '128k',
            'stream_sample_rate' => '44100',
        ];
    }
}
