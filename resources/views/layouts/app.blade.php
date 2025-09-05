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

        <link rel="stylesheet" href="{{ asset('/assets/css/toastr.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @isset ($style)
            {{ $style }}
        @endisset
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
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
        <script src="{{ asset('/assets/js/toastr.min.js') }}"></script>
        <script>
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-center",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            @if (session('success'))
                @foreach (is_array(session('success')) ? session('success') : [session('success')] as $message)
                    toastr.success('{{ $message }}');
                @endforeach
            @endif

            @if (session('error'))
                @foreach (is_array(session('error')) ? session('error') : [session('error')] as $message)
                    toastr.error('{{ $message }}');
                @endforeach
            @endif
        </script>

        @isset($script)
            {{ $script }}
        @endisset

    </body>
</html>
