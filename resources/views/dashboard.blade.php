@php
    $startOfMonth = now()->startOfMonth()->format('Y-m-d H:i:s');
    $endOfMonth   = now()->endOfMonth()->format('Y-m-d H:i:s');
    
    $mostStockRecords  = \App\Models\Stock::with('model')->whereBetween('updated_at', [$startOfMonth, $endOfMonth])->orderBy('stock_qty', 'desc')->limit(3)->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="font-semibold text-lg">{{ __('Top 3 Products with Highest Stock') }}</h3>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                @forelse ($mostStockRecords as $rec)
                    @php
                        $model   = $rec->model;
                        $product = ($model instanceof \App\Models\Product) ? $model : $model->product;
                        $sku     = ($model instanceof \App\Models\Product) ? null : $model;
                    @endphp
                    <a href="{{ route('admin.products.index', ['code' => $product->code]) }}">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg">
                            @if ($product->images->first())
                                <img src="{{ $product->images->first()?->getUrl('thumb') }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded-lg mb-4">
                            @else
                                <div class="w-full h-48 object-cover rounded-lg mb-4"></div>
                            @endif

                            <h4 class="font-medium text-xl mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h4>
                            <p class="items-center text-gray-600 dark:text-gray-400">
                                <p class="text-gray-600 dark:text-gray-400">Color / Size</p>
                                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                    <div class="flex gap-2 items-center">
                                        <span class="w-4 h-4 rounded-sm" style="background-color: {{ $sku?->color?->color_code }};"></span>{{ $sku?->color?->name }}
                                    </div>
                                    <span>/</span>
                                    <div>{{ $sku?->size?->name }}</div>
                                </div>
                            </p>
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    ₹{{ number_format($product->price) }}
                                </p>
                                <p class="text-sm text-gray-500">Stock QTY: <span class="text-gray-900 dark:text-gray-100">{{ $rec->stock_qty }}</span></p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg">
                        <p class="text-gray-900 dark:text-gray-100 text-center">
                            {{ __('No record found!') }}
                        </p>
                    </div>
                @endforelse
            </div>
            
        </div>
    </div>
</x-app-layout>
