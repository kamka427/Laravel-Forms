<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'title' => $this->faker->word(),
            'expires_at' => $this->faker->dateTimeBetween('+1 week', '+1 year'),
            'auth_required' => $this->faker->boolean(),
        ];
    }
}
