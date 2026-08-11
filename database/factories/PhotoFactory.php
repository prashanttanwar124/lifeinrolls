<?php

namespace Database\Factories;

use App\Models\FilmRoll;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'film_roll_id' => FilmRoll::factory(),
            'user_id' => User::factory(),
            'photo_url' => '/storage/photos/'.fake()->uuid().'.jpg',
            'thumbnail_url' => null,
            'caption' => fake()->sentence(3),
            'status' => 'approved',
            'upload_status' => 'ready',
            'download_count' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending_approval']);
    }
}
