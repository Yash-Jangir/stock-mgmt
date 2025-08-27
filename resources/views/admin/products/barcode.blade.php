<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            <!-- Page Content -->
            <main>
                <div style="float: left;width: 100%;height: 100%;">
                    @foreach ($products as $product)
                        @forelse ($product->skus as $sku)
                            <div class="box" style="float: left;width: 50%;height: 150px;padding: 10px;">
                                <div style="height: 20px;">
                                    {{ $product->name }} - [{{ $sku->color?->name }}] - [{{ $sku->size?->name }}]
                                </div>
                                <div style="height: 80px;">
                                    {!! DNS1D::getBarcodeSVG("p:{$product->id}|s:{$sku->id}", 'C128', 3, 80, 'black', false) !!}
                                </div>
                            </div>
                        @empty
                            <div class="box" style="float: left;width: 50%;height: 150px;padding: 10px;">
                                <div style="height: 20px;">
                                    {{ $product->name }}
                                </div>
                                <div style="height: 80px;">
                                    {!! DNS1D::getBarcodeSVG("p:{$product->id}|p:{$product->id}", 'C128', 3, 80, 'black', false) !!}
                                </div>
                            </div>
                        @endforelse
                    @endforeach
                </div>
            </main>
        </div>
    </body>
</html>
