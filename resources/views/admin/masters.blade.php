<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Masters') }}
        </h2>
    </x-slot>

    <div class="grid grid-cols-3 py-12 mx-auto max-w-7xl">
        @foreach ($masters as $master)
            <a href="{{ $master->route }}">
                <div class="w-full sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            {{ $master->title }}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-app-layout>