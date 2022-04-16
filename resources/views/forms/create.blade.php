<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Form') }}
        </h2>
    </x-slot>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uuid/8.3.2/uuid.min.js"
        integrity="sha512-UNM1njAgOFUa74Z0bADwAq8gbTcqZC8Ej4xPSzpnh0l6KMevwvkBvbldF9uR++qKeJ+MOZHRjV1HZjoRvjDfNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>



    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- Create form with name, toggle for availablity and expiration date --}}
                    <form method="POST" action="{{ route('forms.store') }}">

                        @csrf
                        <div class="container flex flex-col gap-y-4">
                            <h2 class="text-xl text-center">Alap mezők</h2>
                            <fieldset>
                                <x-label for="title" class="">{{ __('Az űrlap neve') }}</x-label>
                                <x-input id="title" type="text"
                                    class="block mt-1 w-full @error('title') is-invalid @enderror" name="title"
                                    value="{{ old('title') }}"></x-input>
                            </fieldset>
                            <fieldset>
                                <x-label class="" for="auth_required">
                                    {{ __('Vendégek számára elérhető:') }}
                                    <input class="rounded" type="checkbox" name="auth_required"
                                        id="auth_required" {{ old('auth_required') ? 'checked' : '' }}>
                                </x-label>
                            </fieldset>
                            <fieldset>
                                <x-label for="expires_at">{{ __('Elérhetőség vége') }}</x-label>
                                <x-input type="datetime-local"
                                    class="block mt-1 w-full @error('expires_at') is-invalid @enderror" id="expires_at"
                                    name="expires_at" value="{{ old('expires_at') }}">
                                </x-input>
                            </fieldset>
                            <h2 class="text-xl text-center">Dinamikus csoportok</h2>
                            <div id="groups" class="flex flex-col gap-y-4">
                                @if (old('groups') !== null)
                                    @php
                                        //  var_dump(old('groups'));
                                    @endphp
                                    @foreach (old('groups') as $group)
                                        @php
                                            $uuid = Str::uuid();
                                        @endphp

                                        @if (isset($group['textarea']))
                                            <div class="" id="group_{{ $uuid }}">
                                                <h3 class="">Textarea</h3>
                                                <fieldset>
                                                    <x-label class="" for="required_{{ $uuid }}]">
                                                        Kötelező kitölteni:
                                                        <input class="rounded" type="checkbox" name="groups[{{ $uuid }}][textarea][required]"
                                                        id="required_{{ $uuid }}"
                                                        {{ array_key_exists('required', $group['textarea']) ? 'checked' : '' }}>
                                                        </x-label>
                                                </fieldset>
                                                <fieldset>
                                                    <x-label for="textinput_{{ $uuid }}">Kérdés</x-label>
                                                    <div class="flex items-center gap-x-4">
                                                        <x-input type="text" class="block mt-1 w-full"
                                                            id="textinput_{{ $uuid }}"
                                                            name="groups[{{ $uuid }}][textarea][textinput]"
                                                            placeholder=""
                                                            value="{{ array_key_exists('textinput', $group['textarea']) ? $group['textarea']['textinput'] : '' }}">
                                                        </x-input>
                                                        <x-button type="button"
                                                            class="delete-group btn btn-danger bg-red-600"
                                                            data-group-id="{{ $uuid }}">Kérdés törlése
                                                        </x-button>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        @elseif (isset($group['onechoice']))
                                            <div class="" id="group_{{ $uuid }}">
                                                <h3 class="">One-choice</h3>
                                                <fieldset>
                                                    <x-label class="" for="required_{{ $uuid }}">
                                                        Kötelező kitölteni:
                                                        <input class="rounded" type="checkbox" name="groups[{{ $uuid }}][onechoice][required]"
                                                        id="required_{{ $uuid }}"
                                                        {{ array_key_exists('required', $group['onechoice']) ? 'checked' : '' }}>
                                                        </x-label>
                                                </fieldset>
                                                <fieldset>
                                                    <x-label for="textinput_{{ $uuid }}">Kérdés</x-label>
                                                    <div class="flex items-center gap-x-4 mb-4">
                                                        <x-input type="text" class="block mt-1 w-full"
                                                            id="textinput_{{ $uuid }}"
                                                            name="groups[{{ $uuid }}][onechoice][textinput]"
                                                            placeholder=""
                                                            value="{{ array_key_exists('textinput', $group['onechoice']) ? $group['onechoice']['textinput'] : '' }}">
                                                        </x-input>
                                                        <x-button type="button"
                                                            class="delete-group btn btn-danger bg-red-600"
                                                            data-group-id="{{ $uuid }}">Kérdés törlése
                                                        </x-button>
                                                    </div>
                                                    <div class="options flex flex-col gap-y-4 mb-4">
                                                        <x-label for="choices_{{ $uuid }}"
                                                            data-group-type="one">Válaszlehetőségek
                                                        </x-label>
                                                        @php
                                                            unset($group['onechoice']['textinput']);
                                                            unset($group['onechoice']['required']);
                                                        @endphp
                                                        @foreach ($group as $choice)
                                                            @php
                                                                $uuid2 = Str::uuid();
                                                                //  var_dump($choice);
                                                            @endphp
                                                            @foreach ($choice as $inner)
                                                                @php
                                                                    // var_dump($inner);
                                                                @endphp

                                                                <div class="flex items-center gap-x-4"
                                                                    id="choice_{{ $uuid2 }}">
                                                                    <x-input type="text" class="block mt-1 w-full"
                                                                        id="textinput_{{ $uuid }}"
                                                                        name="groups[{{ $uuid }}][onechoice][{{ $uuid2 }}][textinput]"
                                                                        placeholder=""
                                                                        value="{{ array_key_exists('textinput', $inner) ? $inner['textinput'] : '' }}">
                                                                        ></x-input>
                                                                    <x-button type="button"
                                                                        class="remove-choice btn btn-danger bg-red-600"
                                                                        data-group-id="{{ $uuid }}"
                                                                        data-choice-id="{{ $uuid2 }}">Lehetőség
                                                                        törlése</x-button>
                                                                </div>
                                                            @endforeach
                                                        @endforeach

                                                    </div>
                                                    <x-button type="button"
                                                        class="add-choice w-full btn btn-danger bg-cyan-600"
                                                        data-group-type="one" data-group-id="{{ $uuid }}">
                                                        Lehetőség hozzáadása
                                                    </x-button>
                                                </fieldset>
                                            </div>
                                        @elseif (isset($group['mulchoice']))
                                            <div class="" id="group_{{ $uuid }}">
                                                <h3 class="">Multiple-choice</h3>
                                                <fieldset>
                                                    <x-label class="" for="required_{{ $uuid }}">
                                                        Kötelező kitölteni:
                                                        <input class="rounded" type="checkbox" name="groups[{{ $uuid }}][mulchoice][required]"
                                                        id="required_{{ $uuid }}"
                                                        {{ array_key_exists('required', $group['mulchoice']) ? 'checked' : '' }}>
                                                        </x-label>
                                                </fieldset>
                                                <fieldset>
                                                    <x-label for="textinput_{{ $uuid }}">Kérdés</x-label>
                                                    <div class="flex items-center gap-x-4 mb-4">
                                                        <x-input type="text" class="block mt-1 w-full"
                                                            id="textinput_{{ $uuid }}"
                                                            name="groups[{{ $uuid }}][mulchoice][textinput]"
                                                            placeholder=""
                                                            value="{{ array_key_exists('textinput', $group['mulchoice']) ? $group['mulchoice']['textinput'] : '' }}">
                                                        </x-input>
                                                        <x-button type="button"
                                                            class="delete-group btn btn-danger bg-red-600"
                                                            data-group-id="{{ $uuid }}">Kérdés törlése
                                                        </x-button>
                                                    </div>
                                                    <div class="options flex flex-col gap-y-4 mb-4">
                                                        <x-label for="choices_{{ $uuid }}"
                                                            data-group-type="mul">Válaszlehetőségek
                                                        </x-label>
                                                        @php
                                                            unset($group['mulchoice']['textinput']);
                                                            unset($group['mulchoice']['required']);
                                                        @endphp
                                                        @foreach ($group as $choice)
                                                            @php
                                                                $uuid2 = Str::uuid();
                                                                // var_dump($choice);
                                                            @endphp
                                                            @foreach ($choice as $inner)
                                                                @php
                                                                    // var_dump($inner);
                                                                @endphp

                                                                <div class="flex items-center gap-x-4"
                                                                    id="choice_{{ $uuid2 }}">
                                                                    <x-input type="text" class="block mt-1 w-full"
                                                                        id="textinput_{{ $uuid }}"
                                                                        name="groups[{{ $uuid }}][mulchoice][{{ $uuid2 }}][textinput]"
                                                                        placeholder=""
                                                                        value="{{ array_key_exists('textinput', $inner) ? $inner['textinput'] : '' }}">
                                                                        ></x-input>
                                                                    <x-button type="button"
                                                                        class="remove-choice btn btn-danger bg-red-600"
                                                                        data-group-id="{{ $uuid }}"
                                                                        data-choice-id="{{ $uuid2 }}">Lehetőség
                                                                        törlése</x-button>
                                                                </div>
                                                            @endforeach
                                                        @endforeach
                                                    </div>
                                                    <x-button type="button"
                                                        class="add-choice w-full btn btn-danger bg-cyan-600"
                                                        data-group-type="mul" data-group-id="{{ $uuid }}">
                                                        Lehetőség hozzáadása
                                                    </x-button>
                                                </fieldset>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <div>
                                <x-button class="ml-3 bg-green-600">{{ __('Űrlap mentése') }}</x-button>
                                <x-button type="button" id="add-textarea" class="ml-3">
                                    {{ __('Új kifejtős') }}</x-button>
                                <x-button type="button" id="add-one-choice" class="ml-3">
                                    {{ __('Új feleletválasztós egy válasszal') }}
                                </x-button>
                                <x-button type="button" id="add-multiple-choices" class="ml-3">
                                    {{ __('Új feleletválasztós több válasszal') }}
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const addText = document.querySelector('#add-textarea');
        const addOne = document.querySelector('#add-one-choice');
        const addMul = document.querySelector('#add-multiple-choices');

        const textTemplate = `
			<div class="" id="group_#ID#">
                <h3 class="">Textarea</h3>
                <fieldset>
                    <x-label class="" for="required_#ID#">
                        Kötelező kitölteni:
                        <input class="rounded" type="checkbox" name="groups[#ID#][textarea][required]"
                        id="required_#ID#">
                    </x-label>
                </fieldset>
                <fieldset>
					<x-label for="textinput_#ID#">Kérdés</x-label>
                    <div class="flex items-center gap-x-4">
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textarea][textinput]" placeholder=""></x-input>
                        <x-button type="button" class="delete-group btn btn-danger bg-red-600" data-group-id="#ID#">Kérdés törlése</x-button>
                    </div>
                </fieldset>
			</div>
		`;

        const oneTemplate = `
			<div class="" id="group_#ID#">
                <h3 class="">One-choice</h3>
                <fieldset>
                    <x-label class="" for="required_#ID#">
                        Kötelező kitölteni:
                        <input class="rounded" type="checkbox" name="groups[#ID#][onechoice][required]"
                        id="required_#ID#">
                    </x-label>
                </fieldset>
				<fieldset>
					<x-label for="textinput_#ID#">Kérdés</x-label>
                    <div class="flex items-center gap-x-4 mb-4">
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][onechoice][textinput]" placeholder=""></x-input>
                        <x-button type="button" class="delete-group btn btn-danger bg-red-600" data-group-id="#ID#">Kérdés törlése</x-button>
                    </div>
                    <div class="options flex flex-col gap-y-4 mb-4" >
                        <x-label for="choices_#ID#" data-group-type="one">Válaszlehetőségek</x-label>
				    </div>
                        <x-button type="button" class="add-choice w-full btn btn-danger bg-cyan-600" data-group-type="one" data-group-id="#ID#">Lehetőség hozzáadása</x-button>
                    </fieldset>
			</div>
		`;

        const mulTemplate = `
			<div class="" id="group_#ID#">
                <h3 class="">Multiple-choice</h3>
                <fieldset>
                    <x-label class="" for="required_#ID#">
                        Kötelező kitölteni:
                        <input class="rounded" type="checkbox" name="groups[#ID#][mulchoice][required]"
                        id="required_#ID#">
                    </x-label>
                </fieldset>
				<fieldset>
					<x-label for="textinput_#ID#">Kérdés</x-label>
                    <div class="flex items-center gap-x-4 mb-4">
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][mulchoice][textinput]" placeholder=""></x-input>
                        <x-button type="button" class="delete-group btn btn-danger bg-red-600" data-group-id="#ID#">Kérdés törlése</x-button>
                    </div>
                    <div class="options flex flex-col gap-y-4 mb-4">
                        <x-label for="choices_#ID#" data-group-type="mul">Válaszlehetőségek</x-label>
                    </div>
                        <x-button type="button" class="add-choice w-full btn btn-danger bg-cyan-600" data-group-type="mul" data-group-id="#ID#">Lehetőség hozzáadása</x-button>
                    </fieldset>
			</div>
		`;

        const choiceTemplate = `
        <div class="flex items-center gap-x-4" id="choice_#ID2#">
            <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][#CHOICE#][#ID2#][textinput]" placeholder=""></x-input>
            <x-button type="button" class="remove-choice btn btn-danger bg-red-600" data-group-id="#ID#" data-choice-id="#ID2#">Lehetőség törlése</x-button>
        </div>
		`;



        addText.addEventListener('click', function() {
            let group = document.createElement("div");
            group.innerHTML = textTemplate.replaceAll('#ID#', uuid.v4());
            groups.appendChild(group);
        });
        addOne.addEventListener('click', function() {
            let group = document.createElement("div");
            group.innerHTML = oneTemplate.replaceAll('#ID#', uuid.v4());
            groups.appendChild(group);
        });
        addMul.addEventListener('click', function() {
            let group = document.createElement("div");
            group.innerHTML = mulTemplate.replaceAll('#ID#', uuid.v4());
            groups.appendChild(group);
        });

        document.addEventListener('click', (event) => {
            if (event.target && event.target.classList.contains('delete-group')) {
                const group = document.querySelector(`#group_${event.target.dataset.groupId}`);
                group.remove();
            }
        });

        document.addEventListener('click', (event) => {
            if (event.target && event.target.classList.contains('add-choice')) {
                const group = document.querySelector(`#group_${event.target.dataset.groupId}`).querySelector(
                    '.options');
                let choice = document.createElement("x-input");
                console.log(event.target.dataset.groupType);
                choice.innerHTML = choiceTemplate.replaceAll('#ID#', event.target.dataset.groupId).replaceAll(
                    '#ID2#', uuid.v4()).replaceAll('#CHOICE#', event.target.dataset.groupType === 'one' ?
                    'onechoice' : 'mulchoice');
                // choice.innerHTML = choiceTemplate.replaceAll('#ID2#', uuid.v4());
                console.log(choice);
                group.appendChild(choice);
            }
        });

        document.addEventListener('click', (event) => {
            if (event.target && event.target.classList.contains('remove-choice')) {
                const group = document.querySelector(`#choice_${event.target.dataset.choiceId}`);
                group.remove();
            }
        });
    </script>

</x-app-layout>
