<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Fill: {{ $form->title }}
        </h2>
    </x-slot>

    {{-- @php
       //vardump questions
        $questions = $form->questions;
        foreach ($questions as $question) {
            var_dump($question);
        }
    @endphp --}}


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">

                <div class="bg-white flex flex-col gap-6 rounded">
                    <div class="p-6 bg-white border border-gray-200 shadow-sm sm:rounded-lg">

                        <h1 class="text-xl">{{ $form->title }}</h1>

                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('forms.response') }}">
                        @csrf

                        {{-- @php
                            var_dump(old());

                        @endphp --}}
                        @foreach ($form->questions as $question)
                            <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
                                {{-- <h2 class="text-xl">{{ $question->question }}</h2> --}}
                                @if ($question->answer_type == 'TEXTAREA')
                                {{-- @php
                                    var_dump(old('groups'));
                                @endphp --}}
                                    <fieldset>
                                        <x-label for="question_{{ $question->id }}" class="">
                                            {{ $question->question }}
                                            {{ $question->required ? '*' : '' }}</x-label>
                                        <x-input id="question_{{ $question->id }}" type="text"
                                            class="block mt-1 w-full" name="groups[{{ $question->id }}][TEXTAREA][answer]"
                                            value="{{ old('groups.' . $question->id . '.TEXTAREA.answer') }}"></x-input>
                                    </fieldset>
                                @elseif ($question->answer_type == 'ONE_CHOICE')
                                    <fieldset>
                                        <x-label for="question_{{ $question->id }}" class="">
                                            {{ $question->question }}
                                            {{ $question->required ? '*' : '' }}</x-label>
                                        @foreach ($question->choices as $choice)
                                            <div class="flex flex-col">
                                                <fieldset>
                                                    <input id="question_{{ $question->id }}" type="radio"
                                                        class=""
                                                        name="groups[{{ $question->id }}][ONE_CHOICE][choice]"
                                                        value="{{ $choice->id }}"
                                                        {{ old('groups.' . $question->id . '.choice') == $choice->id ? 'checked' : '' }}>
                                                    {{ $choice->choice }}
                                                </fieldset>
                                            </div>
                                        @endforeach
                                    </fieldset>
                                @elseif ($question->answer_type == 'MULTIPLE_CHOICES')
                                    <fieldset>
                                        <x-label for="question_{{ $question->id }}" class="">
                                            {{ $question->question }}
                                            {{ $question->required ? '*' : '' }}</x-label>
                                        @foreach ($question->choices as $choice)
                                            <div class="flex flex-col">
                                                <fieldset>
                                                    <input id="question_{{ $question->id }}" type="checkbox"
                                                        class=""
                                                        name="groups[{{ $question->id }}][MULTIPLE_CHOICES][choices][{{ $choice->id }}]"
                                                        value="{{ $choice->id }}"
                                                        {{ old('groups.' . $question->id . '.choices.' . $choice->id) == $choice->id ? 'checked' : '' }}>
                                                    {{ $choice->choice }}
                                                </fieldset>
                                            </div>
                                        @endforeach
                                    </fieldset>
                                @endif
                            </div>
                        @endforeach
                        <div>
                            <x-button class="ml-3 bg-green-600">{{ __('Űrlap mentése') }}</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
