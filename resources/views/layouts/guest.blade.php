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
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <h1 class="text-3xl text-gray-900 dark:text-gray-100">{{ request()->routeIs('register') ? __('Register') : __('Login') }}</h1>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
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
    </body>
</html>
