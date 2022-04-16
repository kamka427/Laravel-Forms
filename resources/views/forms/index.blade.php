<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Forms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-6">
                @foreach ($forms as $form)
                    <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg mx-6">

                        {{-- @php
                            var_dump($forms);
                        @endphp --}}
                        <h1 class="text-xl">{{ $form->title }}</h1>
                        <p class="text-sm">Módosítva: {{ $form->created_at }}</p>
                    </div>
                @endforeach
                <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg mx-auto">
                    {{ $forms->links() }}
                </div>

            </div>
        </div>
</x-app-layout>
