<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Form') }}
        </h2>
    </x-slot>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/uuid/8.3.2/uuid.min.js" integrity="sha512-UNM1njAgOFUa74Z0bADwAq8gbTcqZC8Ej4xPSzpnh0l6KMevwvkBvbldF9uR++qKeJ+MOZHRjV1HZjoRvjDfNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


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
                        <h2>Alap mezők</h2>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            {{-- checkbox to turn on form for guests --}}
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="guest_access" id="guest_access" value="1" {{ old('guest_access') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="guest_access">
                                        {{ __('Guest access') }}
                                    </label>
                                </div>
                            </div>
                            {{-- set expiration date with date picker --}}
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="expiration_date">{{ __('Expiration date') }}</label>
                                    <input type="datetime-local" class="form-control" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                                </div>
                            </div>
                        </div>

                        <h2>Dinamikus csoportok</h2>
                        <div class="card p-3 mt-2 mb-3">
                            <div id="groups">
                                {{-- Az előző group-ok újrarenderelése, mivel ha a validator visszadobja a formot, az egy új oldalt ad, vagyis a js-el hozzáadott elemek elvesznek --}}
                                @if (old('groups') !== null)
                                    @foreach (old('groups') as $group)
                                        @php
                                            $uuid = Str::uuid();
                                        @endphp
                                        <div class="mb-3" id="group_{{ $uuid }}">
                                            <h4>Új csoport</h4>
                                            <div class="card p-3 mt-2 mb-3">
                                                <div class="form-group mb-3">
                                                    <label for="textinput_{{ $uuid }}">Beviteli mező</label>
                                                    <input type="text" class="form-control" id="textinput_{{ $uuid }}" name="groups[{{ $uuid }}][textinput]" placeholder="Csoport beviteli mezeje" value="{{ array_key_exists('textinput', $group) ? $group['textinput'] : '' }}">
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="selector_{{ $uuid }}">Választás</label>
                                                    <select id="selector_{{ $uuid }}" name="groups[{{ $uuid }}][selector]" class="form-select">
                                                        <option value="" disabled selected>Válassz valamit</option>
                                                        <option value="one" @if(array_key_exists('selector', $group) && $group['selector'] === 'one') selected @endif>One</option>
                                                        <option value="two" @if(array_key_exists('selector', $group) && $group['selector'] === 'two') selected @endif>Two</option>
                                                        <option value="three" @if(array_key_exists('selector', $group) && $group['selector'] === 'three') selected @endif>Three</option>
                                                    </select>
                                                </div>
                                                <div class="d-flex justify-content-center">
                                                    <button type="button" class="delete-group btn btn-danger" data-group-id="{{ $uuid }}">Csoport törlése</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-secondary" id="add-group">Új csoport</button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-secondary">Űrlap elküldése</button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
        <script>
            // Template a group-hoz
            const template = `
                <div class="mb-3" id="group_#ID#">
                    <h4>Új csoport</h4>
                    <div class="card p-3 mt-2 mb-3">
                        <div class="form-group mb-3">
                            <label for="textinput_#ID#">Beviteli mező</label>
                            <input type="text" class="form-control" id="textinput_#ID#" name="groups[#ID#][textinput]" placeholder="Csoport beviteli mezeje">
                        </div>
                        <div class="form-group mb-3">
                            <label for="selector_#ID#">Választás</label>
                            <select id="selector_#ID#" name="groups[#ID#][selector]" class="form-select">
                                <option value="" disabled selected>Válassz valamit</option>
                                <option value="one">One</option>
                                <option value="two">Two</option>
                                <option value="three">Three</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="delete-group btn btn-danger" data-group-id="#ID#">Csoport törlése</button>
                        </div>
                    </div>
                </div>
            `;

            const groups = document.querySelector('#groups');
            const addGroup = document.querySelector('button#add-group');
            addGroup.addEventListener('click', (event) => {
                let group = document.createElement("div");
                group.innerHTML = template.replaceAll('#ID#', uuid.v4());
                groups.appendChild(group);
            });
            // Általános esemény, mivel a delete-group-okat dinamikusan adjuk hozzá
            document.addEventListener('click', (event) => {
                if(event.target && event.target.classList.contains('delete-group')) {
                    //console.log(event.target.dataset);
                    const group = document.querySelector(`#group_${event.target.dataset.groupId}`);
                    group.remove();
                }
            });
        </script>
</x-app-layout>
