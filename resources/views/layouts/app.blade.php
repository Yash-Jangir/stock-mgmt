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
            <div id="toastr-wrapper" class="fixed inline-flex flex-col top-0 left-1/2 transform -translate-x-1/2 z-[50]">
                @if (session('success'))
                    @foreach (is_array(session('success')) ? session('success') : [session('success')] as $message)
                        <div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2500)"
                            class="toast-success mt-4 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <span>{{ $message }}</span>
                                <button onclick="this.closest('.toast-success').remove()" class="text-white hover:text-gray-200">&times;</button>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if (session('error'))
                    @foreach (is_array(session('error')) ? session('error') : [session('error')] as $message)
                        <div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2500)"
                            class="toast-err z-[9] mt-4 px-4 py-2 bg-red-500 text-white rounded shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <span>{{ $message }}</span>
                                <button onclick="this.closest('.toast-err').remove()" class="text-white hover:text-gray-200">&times;</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

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
