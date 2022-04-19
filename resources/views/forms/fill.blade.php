<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kitöltés: {{ $form->title }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                @isset($expired)
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm sm:rounded-lg"
                        role="alert">
                        <p class="font-bold">
                            Az űrlap lejárt {{ $expired }} időpontban! Már nem lehet kitölteni!
                        </p>

                    </div>
                @else
                    <div class="bg-white flex flex-col gap-6 rounded">
                        <div class="p-6 bg-white border border-gray-200 shadow-sm sm:rounded-lg">

                            <h1 class="text-xl">{{ $form->title }}</h1>

                        </div>
                        {{-- @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    @foreach ($errors->keys() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}
                        <form method="POST" action="{{ route('forms.response', $form->id) }}">
                            @csrf


                            @foreach ($form->questions as $question)
                                <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
                                    @if ($question->answer_type === 'TEXTAREA')
                                        <fieldset>
                                            <label for="question_{{ $question->id }}" class="text-lg">
                                                {{ $question->question }}
                                                {{ $question->required ? '*' : '' }}</label>
                                            @error('groups.' . $question->id . '.' . $question->required .
                                                '.TEXTAREA.answer')
                                                <div class="alert alert-danger text-red-600" role="alert">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <input id="question_{{ $question->id }}" type="text"
                                                class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full @error('groups.' . $question->id . '.' . $question->required . '.TEXTAREA.answer') border-red-600 @enderror"
                                                name="groups[{{ $question->id }}][{{ $question->required }}][TEXTAREA][answer]"
                                                value="{{ old('groups.' . $question->id . '.' . $question->required . '.TEXTAREA.answer') }}">

                                        </fieldset>
                                    @elseif ($question->answer_type === 'ONE_CHOICE')
                                        <fieldset>
                                            <label for="question_{{ $question->id }}" class="text-lg">
                                                {{ $question->question }}
                                                {{ $question->required ? '*' : '' }}</label>
                                            @error('groups.' . $question->id . '.' . $question->required .
                                                '.ONE_CHOICE.choice')
                                                <div class="alert alert-danger text-red-600" role="alert">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            @foreach ($question->choices as $choice)
                                                <div class="flex flex-col">
                                                    <fieldset>

                                                        @if ($question->required)
                                                            <input type="hidden"
                                                                name="groups[{{ $question->id }}][{{ $question->required }}][ONE_CHOICE][req]"
                                                                value="true">
                                                        @endif
                                                        <input id="question_{{ $question->id }}" type="radio"
                                                            class=""
                                                            name="groups[{{ $question->id }}][{{ $question->required }}][ONE_CHOICE][choice]"
                                                            value="{{ $choice->id }}"
                                                            {{ old('groups.' . $question->id . '.' . $question->required . '.ONE_CHOICE.choice') == $choice->id? 'checked': '' }}>
                                                        {{ $choice->choice }}
                                                    </fieldset>
                                                </div>
                                            @endforeach
                                        </fieldset>
                                    @elseif ($question->answer_type === 'MULTIPLE_CHOICES')
                                        <fieldset>
                                            <label for="question_{{ $question->id }}" class="text-lg">
                                                {{ $question->question }}
                                                {{ $question->required ? '*' : '' }}</label>
                                            @error('groups.' . $question->id . '.' . $question->required .
                                                '.MULTIPLE_CHOICES.choices')
                                                <div class="alert alert-danger text-red-600" role="alert">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            @foreach ($question->choices as $choice)
                                                <div class="flex flex-col">
                                                    <fieldset>
                                                        @if ($question->required)
                                                            <input type="hidden"
                                                                name="groups[{{ $question->id }}][{{ $question->required }}][MULTIPLE_CHOICES][req]"
                                                                value="true">
                                                        @endif
                                                        <input id="question_{{ $question->id }}" type="checkbox"
                                                            class=""
                                                            name="groups[{{ $question->id }}][{{ $question->required }}][MULTIPLE_CHOICES][choices][{{ $choice->id }}]"
                                                            value="{{ $choice->id }}"
                                                            {{ old('groups.' . $question->id . '.' . $question->required . '.MULTIPLE_CHOICES.choices.' . $choice->id) ==$choice->id? 'checked': '' }}>
                                                        {{ $choice->choice }}
                                                    </fieldset>
                                                </div>
                                            @endforeach
                                        </fieldset>
                                    @endif
                                </div>
                            @endforeach

                            <div class="py-6 text-center">
                                <x-button class="ml-3 bg-green-600">Küldés</x-button>
                                <x-button type="reset" class="ml-3 bg-red-600">Törlés</x-button>
                            </div>
                        </form>
                    </div>
                @endisset
            </div>
        </div>
    </div>


</x-app-layout>
