<?php

namespace Database\Factories;

use App\Models\FilmRoll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FilmRoll>
 */
class FilmRollFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'invite_code' => strtoupper(Str::random(8)),
            'invite_token' => Str::random(40),
            'max_photos' => 36,
            'current_photos' => 0,
            'roll_type' => 'standard',
            'status' => 'active',
        ];
    }

    public function mode(string $mode): static
    {
        return $this->state(fn () => ['roll_type' => $mode]);
    }
}
