<?php

namespace Database\Factories;

use App\Models\ReadingLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReadingLogFactory extends Factory
{
    protected $model = ReadingLog::class;

    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'volume_id'     => fake()->bothify('??########'),
            'title'         => fake()->sentence(3),
            'authors'       => fake()->name(),
            'thumbnail_url' => null,
            'isbn'          => null,
            'status'        => fake()->randomElement(['wishlist', 'reading', 'read', 'dropped']),
            'rating'        => null,
            'review'        => null,
        ];
    }
}
