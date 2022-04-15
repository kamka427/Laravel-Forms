<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class FormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => Str::ucfirst($this->faker->words($this->faker->numberBetween(3, 8), true)),
            'expires_at' => $this->faker->dateTimeBetween('+1 week', '+1 year'),
            'auth_required' => $this->faker->boolean(),
        ];
    }
}
