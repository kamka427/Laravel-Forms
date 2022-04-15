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
                                <x-label for="name" class="">{{ __('Az űrlap neve') }}</x-label>
                                <x-input id="name" type="text"
                                    class="block mt-1 w-full @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}"></x-input>
                            </fieldset>
                            <fieldset>
                                <x-label class="" for="guest_access">
                                    {{ __('Vendégek számára elérhető:') }}
                                    <input class="rounded" type="checkbox" name="guest_access" id="guest_access"
                                        {{ old('guest_access') ? 'checked' : '' }}>
                                </x-label>
                            </fieldset>
                            <fieldset>
                                <x-label for="expiration_date">{{ __('Elérhetőség vége') }}</x-label>
                                <x-input type="datetime-local"
                                    class="block mt-1 w-full @error('expiration_date') is-invalid @enderror"
                                    id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                                </x-input>
                            </fieldset>
                            <h2 class="text-xl text-center">Dinamikus csoportok</h2>
                            <div id="groups" class="flex flex-col gap-y-4">
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
					<x-label for="textinput_#ID#">Kérdés</x-label>
                    <div class="flex items-center gap-x-4">
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>
                        <x-button type="button" class="delete-group btn btn-danger bg-red-600" data-group-id="#ID#">Kérdés törlése</x-button>
                    </div>
                </fieldset>
			</div>
		`;

        const oneTemplate = `
			<div class="" id="group_#ID#">
                <h3 class="">One-choice</h3>
				<fieldset>
					<x-label for="textinput_#ID#">Kérdés</x-label>
                    <div class="flex items-center gap-x-4 mb-4">
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>
                        <x-button type="button" class="delete-group btn btn-danger bg-red-600" data-group-id="#ID#">Kérdés törlése</x-button>
                    </div>
                    <div class="options flex flex-col gap-y-4 mb-4">
                        <x-label for="choices_#ID#">Válaszlehetőségek</x-label>
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>

                    </div>
                        <x-button type="button" class="add-choice w-full btn btn-danger bg-cyan-600" data-group-id="#ID#">Lehetőség hozzáadása</x-button>
                    </fieldset>
			</div>
		`;

        const mulTemplate = `
			<div class="" id="group_#ID#">
                <h3 class="">Multiple-choice</h3>
				<fieldset>
					<x-label for="textinput_#ID#">Kérdés</x-label>
                    <div class="flex items-center gap-x-4 mb-4">
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>
                        <x-button type="button" class="delete-group btn btn-danger bg-red-600" data-group-id="#ID#">Kérdés törlése</x-button>
                    </div>
                    <div class="options flex flex-col gap-y-4 mb-4">
                        <x-label for="choices_#ID#">Válaszlehetőségek</x-label>
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>
					    <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>

                    </div>
                        <x-button type="button" class="add-choice w-full btn btn-danger bg-cyan-600" data-group-id="#ID#">Lehetőség hozzáadása</x-button>
                    </fieldset>
			</div>
		`;

        const choiceTemplate = `
        <div class="flex items-center gap-x-4" id="choice_#ID2#">
            <x-input type="text" class="block mt-1 w-full" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder=""></x-input>
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
                const group = document.querySelector(`#group_${event.target.dataset.groupId}`).querySelector('.options');
                let choice = document.createElement("x-input");
                choice.innerHTML = choiceTemplate.replaceAll('#ID#',`#group_${event.target.dataset.groupId}`);
                choice.innerHTML = choiceTemplate.replaceAll('#ID2#',uuid.v4());
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
