<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Fill: {{ $form->title }}
        </h2>
    </x-slot>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                @if (new DateTime($form->expires_at) < new DateTime())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm sm:rounded-lg"
                        role="alert">
                        <p class="font-bold">
                            {{ __('Form expired!') }}
                        </p>
                        <p class="text-sm">
                            {{ __('The form is expired and can no longer be filled.') }}
                        </p>
                    </div>
                @else
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
                                                class="block mt-1 w-full"
                                                name="groups[{{ $question->id }}][TEXTAREA][answer]"
                                                value="{{ old('groups.' . $question->id . '.TEXTAREA.answer') }}">
                                            </x-input>
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
                            <div class="py-6 text-center">
                                <x-button class="ml-3 bg-green-600">{{ __('Send') }}</x-button>
                                <x-button type="reset" class="ml-3 bg-red-600">{{ __('Reset') }}</x-button>
                            </div>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>


</x-app-layout>
