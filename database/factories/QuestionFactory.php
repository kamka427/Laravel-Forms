<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question' => Str::ucfirst($this->faker->sentence()),
            'answer_type' => $this->faker->randomElement(['TEXTAREA', 'ONE_CHOICE', 'MULTIPLE_CHOICES']),
            'required' => $this->faker->boolean(),
        ];
    }
}
