<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Főoldal
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Üdvözöljük {{ Auth::user()->name }}!
                </div>
            </div>


            @if (Session::has('form-created'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm sm:rounded-lg"
                    role="alert">
                    <p class="font-bold">
                        Sikeres létrehozás!
                    </p>
                    <a href="{{ Session::get('form-created') }}">Az űrlap linkje
                        ({{ Session::get('form-created') }})</a>
                </div>
            @endif
            @if (Session::has('form-filled'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm sm:rounded-lg"
                    role="alert">
                    <p class="font-bold">
                        Sikeresen kitöltötte a {{Session::get('form-filled')}} című űrlapot!
                    </p>
                </div>
            @endif
            @if (Session::has('form-deleted'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 shadow-sm sm:rounded-lg"
                    role="alert">
                    <p class="font-bold">
                        Sikeresen kitörölte a {{Session::get('form-deleted')}} című űrlapot!
                    </p>
                </div>
            @endif
            @if (Session::has('form-restored'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm sm:rounded-lg"
                    role="alert">
                    <p class="font-bold">
                        Sikeresen visszaállította a {{Session::get('form-restored')}} című űrlapot!
                    </p>
                </div>
            @endif

            <div class="flex gap-6">
                <a href="{{ route('forms.create') }}"
                    class="basis-1/2 text-blue-500 hover:text-blue-700 p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
                    Új űrlap
                </a>
                <a href="{{ route('forms.index') }}"
                    class="basis-1/2 text-blue-500 hover:text-blue-700 p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg">
                    Meglévő űrlapok kezelése
                </a>
            </div>
        </div>
</x-app-layout>
