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
        $users_count = $faker->numberBetween(5, 10);

        for ($i = 1; $i <= $users_count; $i++) {
            $users->add(
                User::factory()->create([
                    'name' => 'user' . $i,
                    'email' => 'user' . $i . '@szerveroldali.hu',
                ])
            );
        }

        $forms = Form::factory($faker->numberBetween(6, 12))->create();
        $forms->each(
            function ($form) use (&$users) {
                if ($users->isNotEmpty()) {
                    $form->creator()->associate($users->random());
                    $form->save();
                }
            }
        );

        foreach ($forms as $form) {


            $questions = Question::factory($faker->numberBetween(6, 12))->create([
                'form_id' => $form->id,
            ]);

            foreach ($questions as $question) {
                if ($question->answer_type == 'TEXTAREA') {
                    Answer::factory(1)->create([
                        'question_id' => $question->id,
                        'user_id' => $users->random()->id,
                        'answer' => $faker->text,
                    ]);
                } elseif ($question->answer_type == 'ONE_CHOICE') {
                    $choices_number = $faker->numberBetween(2, 5);
                    Choice::factory($choices_number)->create([
                        'question_id' => $question->id,
                    ]);
                    Answer::factory(1)->create([
                        'question_id' => $question->id,
                        'user_id' => $users->random()->id,
                        'choice_id' => $faker->numberBetween(1, $choices_number)
                    ]);
                } elseif ($question->answer_type == 'MULTIPLE_CHOICES') {
                    $choices_number = $faker->numberBetween(2, 5);
                    Choice::factory($choices_number)->create([
                        'question_id' => $question->id,
                    ]);
                    $answers_number = $faker->numberBetween(2, $choices_number);
                    $user_id = $users->random()->id;
                    for ($i = 0; $i < $answers_number; $i++) {
                        Answer::factory(1)->create([
                            'question_id' => $question->id,
                            'user_id' => $user_id,
                            'choice_id' => $faker->numberBetween(1, $choices_number)
                        ]);
                    }
                }
            }
        }
    }
}
