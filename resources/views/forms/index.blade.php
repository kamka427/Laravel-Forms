<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Űrlapok kezelése
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                @foreach ($forms as $form)
                    <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg flex justify-between">
                        <div class="flex flex-col gap-2">
                            <h1 class="text-xl">{{ $form->title }}</h1>
                            <p class="text-sm">Módosítva: {{ $form->updated_at }}</p>
                            <p class="text-sm">Létrehozva: {{ $form->created_at }}</p>
                        </div>
                        @if (!$form->trashed())
                            <x-a class="self-center py-3" href="{{ route('forms.show', $form->id) }}">
                                <span>Megnyitás</span>
                            </x-a>
                        @else
                            <x-button class="self-center py-3 bg-green-600" href="{{ route('forms.restore', $form->id) }}"
                                onclick=" document.getElementById('restore-form').submit()">
                                <span>Visszaállítás</span>
                            </x-button>


                            <form id="restore-form" action="{{ route('forms.restore', $form->id) }}" method="POST"
                                class="hidden">
                                @method('PATCH')
                                @csrf
                            </form>
                        @endif

                    </div>
                @endforeach

            </div>

            @if ($forms->hasPages())
                <div class="mt-6 p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg mx-auto">
                    {{ $forms->links() }}
                </div>
            @endif
        </div>
</x-app-layout>
