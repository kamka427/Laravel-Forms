<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Show: {{ $form->title }}
        </h2>
    </x-slot>

    @php
        $hasAnswers = false;
        $questions = $form->questions;
        foreach ($questions as $question) {
            $answers = $question->answers;
            if (count($answers) > 0) {
                $hasAnswers = true;
                break;
            }
        }
    @endphp


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg flex justify-between">
                    <h1 class="text-xl">Az űrlapot még senki sem töltötte ki, ezért módosítható.</h1>
                    <x-a href="{{ route('forms.edit', $form) }}">
                        <span>Módosítás</span>
                    </x-a>
                    @if (!$hasAnswers)
                    @else
                        <h1 class="text-xl">Az űrlapot már kitöltötték, ezért nem módosítható.</h1>
                    @endif

                </div>
                <div class="bg-white flex flex-col gap-6 rounded">
                <div class="p-6 bg-white border border-gray-200 shadow-sm sm:rounded-lg">
                    <h1 class="text-xl">Statisztika</h1>

                </div>
                @foreach ($form->questions as $question)
                    <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
                        <h1 class="text-xl">{{ $question->question }}</h1>
                        <p class="text-sm">{{ $question->answer_type }}</p>
                        <p class="text-sm">{{ $question->type }}</p>
                        <p class="text-sm">{{ $question->required ? 'Mandatory' : 'Optional' }}</p>
                        <h2 class="mb-3">Answers:</h2>
                        <div class="flex flex-col gap-3">
                            @if ($question->answer_type === 'TEXTAREA')
                                @forelse ($question->answers as $answer)
                                    <p class="text-sm">
                                        {{ $answer->user->name ? $answer->user->name : 'Vendég' }}:
                                        {{ $answer->answer }}</p>
                                @empty
                                    <p class="text-sm">No answers yet.</p>
                                @endforelse
                            @else
                                @php
                                    $sorted = $question->choices
                                        ->sortByDesc(function ($choice) {
                                            return $choice->answers->count();
                                        })
                                        ->values()
                                        ->all();

                                @endphp
                                @forelse ($sorted as $choice)
                                    <p class="text-sm">{{ $choice->choice }}
                                        ({{ $choice->answers->count() }})
                                    </p>
                                @empty
                                    <p class="text-sm">No answers yet.</p>
                                @endforelse
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>
            </div>
        </div>
    </div>
</x-app-layout>
