<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Adatok: {{ $form->title }}
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
                    @if (!$hasAnswers)
                        <h1 class="text-xl">Az űrlapot még senki sem töltötte ki, ezért módosítható.</h1>

                        <div>
                            <x-a href="{{ route('forms.edit', $form) }}">
                                <span>Módosítás</span>
                            </x-a>
                            <x-a href="{{ route('forms.fill', $form) }}">
                                <span>Kitöltés</span>
                            </x-a>

                            <x-button class="bg-red-600"
                                onclick="document.getElementById('delete-form').submit()">
                                <span>Törlés</span>
                            </x-button>

                        </div>
                        <form id="delete-form" class="hidden" action="{{ route('forms.destroy', $form->id) }}"
                            method="POST">
                            @method('DELETE')
                            @csrf
                        </form>
                    @else
                        <h1 class="text-xl">Az űrlapot már kitöltötték, ezért nem módosítható.</h1>
                        <x-a href="{{ route('forms.fill', $form) }}">
                            <span>Kitöltés</span>
                        </x-a>
                    @endif

                </div>
                <div class="bg-white flex flex-col gap-6 rounded">
                    <div class="p-6 bg-white border border-gray-200 shadow-sm sm:rounded-lg felx flex-col gap-2">
                        <h1 class="text-2xl mb-4">{{ $form->title }}</h1>
                        <p class="text-sm">Kitöltheti:
                            {{ $form->auth_required ? 'Csak regisztrált felhasználók' : 'Regisztrált felhasználók és vendégek' }}
                        </p>
                        <p class="text-sm">Létrehozva: {{ $form->created_at }}</p>
                        <p class="text-sm">Módosítva: {{ $form->updated_at }}</p>
                        <p class="text-sm"> Link a kitöltéshez:
                            <a href="{{ url()->current() }}/fill"
                                class="text-blue-500 hover:text-blue-700">{{ url()->current() }}/fill</a>
                        </p>
                    </div>
                </div>
                <div class="bg-white flex flex-col gap-6 rounded">
                    <div class="p-6 bg-white border border-gray-200 shadow-sm sm:rounded-lg">
                        <h1 class="text-xl">Statisztika kérdésekre bontva</h1>
                    </div>
                    @foreach ($form->questions as $question)
                        <div
                            class="p-6 pt-0 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg flex flex-col gap-1">
                            <h1 class="text-xl">{{ $question->question }}</h1>
                            <div class="flex gap-1">
                                <p class="text-sm">{{ $question->answer_type }},</p>
                                <p class="text-sm @if ($question->required) text-red-600 @endif">
                                    {{ $question->required ? 'Kötelező' : 'Opcionális' }},</p>
                                <p class="text-sm">Módostva: {{ $question->updated_at }}</p>
                            </div>

                            <h2 class="mb-1 text-lg">Válaszok:</h2>
                            <div class="flex flex-col gap-3">
                                @if ($question->answer_type === 'TEXTAREA')
                                    @forelse ($question->answers as $answer)
                                        <p class="text-base">
                                            {{ $answer->user->name ?? 'Vendég' }}:
                                            {{ $answer->answer }}</p>
                                    @empty
                                        <p class="text-base italic">Még nem érkeztek válaszok a kérdésre.</p>
                                    @endforelse
                                @else
                                    @php
                                        $sorted = $question->choices->sortByDesc(function ($choice) {
                                            return $choice->answers->count();
                                        });
                                        $hasAnswers = false;
                                        foreach ($sorted as $choice) {
                                            if ($choice->answers->count() > 0) {
                                                $hasAnswers = true;
                                                break;
                                            }
                                        }

                                    @endphp
                                    @foreach ($sorted as $choice)
                                        <p class="text-base">{{ $choice->choice }}
                                            ({{ $choice->answers->count() }})
                                        </p>
                                    @endforeach
                                    @if (!$hasAnswers)
                                        <p class="text-base italic">Még nem érkeztek válaszok a kérdésre.</p>
                                    @endif
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
</x-app-layout>
