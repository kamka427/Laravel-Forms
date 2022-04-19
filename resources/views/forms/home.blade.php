<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Welcome {{ Auth::user()->name }}!
                </div>
            </div>
            @if (Session::has('form-created'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p class="font-bold">
                        {{ __('Form created successfully!') }}
                    </p>
                    <a href="{{ Session::get('form-created') }}">The link of the form({{ Session::get('form-created') }})</a>
                    <p class="text-sm">
                        {{ __('You can now edit the form.') }}
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
