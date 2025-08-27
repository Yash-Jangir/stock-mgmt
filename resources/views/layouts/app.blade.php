<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="darkMode()"
    x-init="init()"
    :class="{'dark': isDark}"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @isset ($style)
            {{ $style }}
        @endisset
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        
            @if (session('success'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="toast-success absolute z-[9] top-0 left-1/2 transform -translate-x-1/2 mt-4 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
                    <div class="flex items-center justify-between gap-4">
                        <span>{{ session('success') }}</span>
                        <button onclick="this.closest('.toast-success').remove()" class="text-white hover:text-gray-200">&times;</button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="toast-err absolute z-[9] top-0 left-1/2 transform -translate-x-1/2 mt-4 px-4 py-2 bg-red-500 text-white rounded shadow-lg">
                    <div class="flex items-center justify-between gap-4">
                        <span>{{ session('error') }}</span>
                        <button onclick="this.closest('.toast-err').remove()" class="text-white hover:text-gray-200">&times;</button>
                    </div>
                </div>
            @endif

            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            function darkMode() {
                return {
                    isDark: false,

                    init() {
                        this.isDark = localStorage.getItem('theme') === 'dark'
                            || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
                    },

                    toggle() {
                        this.isDark = !this.isDark
                        localStorage.setItem('theme', this.isDark ? 'dark' : 'light')
                    }
                }
            }
        </script>
        <script src="{{ asset('/assets/js/jquery.min.js') }}"></script>
        @isset($script)
            {{ $script }}
        @endisset

    </body>
</html>
