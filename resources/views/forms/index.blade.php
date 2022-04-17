<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Forms') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6">
                @foreach ($forms as $form)
                    <div class="p-6 bg-white border-b border-gray-200 shadow-sm sm:rounded-lg flex justify-between">

                        <div>
                            <h1 class="text-xl">{{ $form->title }}</h1>
                            <p class="text-sm">Módosítva: {{ $form->created_at }}</p>
                        </div>
                        <x-a href="{{ route('forms.show', $form) }}">
                            <span>View form</span>
                        </x-a>
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
