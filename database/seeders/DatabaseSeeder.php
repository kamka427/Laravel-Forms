<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Form;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Choice;
use Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $faker = \Faker\Factory::create();

        DB::table('users')->truncate();
        DB::table('forms')->truncate();
        DB::table('questions')->truncate();
        DB::table('answers')->truncate();
        DB::table('choices')->truncate();

        $users = collect();

        for ($i = 1; $i <= $faker->numberBetween(7, 14); $i++) {
            $users->add(
                User::factory()->create([
                    'name' => 'user' . $i,
                    'email' => 'user' . $i . '@szerveroldali.hu',
                ])
            );
        }

        $forms = collect();
        foreach ($users as $user) {
            for ($i = 0; $i < $faker->numberBetween(6, 24); $i++) {
                $forms->add(
                    Form::factory()->create([
                        'created_by' => $user->id,
                    ])
                );
            }
        }

        $questions = collect();
        foreach ($forms as $form) {
            for ($i = 0; $i < $faker->numberBetween(5, 10); $i++) {
                $questions->add(
                    Question::factory()->create([
                        'form_id' => $form->id,
                    ])
                );
            }
        }

        $choices = collect();
        foreach ($questions as $question) {
            if ($question->answer_type !== 'TEXTAREA') {
                for ($i = 0; $i < $faker->numberBetween(5, 10); $i++) {
                    $choices->add(
                        Choice::factory()->create([
                            'question_id' => $question->id,
                        ])
                    );
                }
            }
        }


        $answers = collect();
        for ($i = 0; $i < $faker->numberBetween(7, $user->count()); $i++) {
            foreach ($questions as $question) {
                if ($question->form->auth_required) {
                    $filler = $users->random();
                } else {
                    $is_guest = $faker->boolean(20);
                    $filler = $is_guest ? null : $users->random();
                }
                if (!$question->required) {
                    if ($faker->boolean(50)) {
                        continue;
                    }
                }

                if ($question->answer_type === 'TEXTAREA') {
                    $answers->add(
                        Answer::factory()->create([
                            'question_id' => $question->id,
                            'user_id' => $filler ? $filler->id : null,
                            'answer' => $faker->text($faker->numberBetween(10, 20)),

                        ])
                    );
                } elseif ($question->answer_type === 'ONE_CHOICE') {
                    $answers->add(
                        Answer::factory()->create([
                            'question_id' => $question->id,
                            'user_id' => $filler ? $filler->id : null,
                            'choice_id' => $question->choices->random()->id,
                        ])
                    );
                } elseif ($question->answer_type === 'MULTIPLE_CHOICES') {
                    $available_choices = $question->choices->pluck('id')->toArray();
                    for ($j = 0; $j < $faker->numberBetween(1, $question->choices->count()); $j++) {
                        shuffle($available_choices);
                        $answers->add(
                            Answer::factory()->create([
                                'question_id' => $question->id,
                                'user_id' => $filler ? $filler->id : null,
                                'choice_id' => array_pop($available_choices),
                            ])
                        );
                    }
                }
            }
        }
    }
}
