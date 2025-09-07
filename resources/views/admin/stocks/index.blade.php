<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Stock List') }}
            </h2>
            <div class="flex items-center justify-end gap-2">
                <!-- {{-- <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <x-secondary-button id="scan-barcode">{{ __('SCAN') }}</x-secondary-button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('admin.stocks.create', ['type' => 'in'])">
                            {{ __('Stock-In') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('admin.stocks.create', ['type' => 'out'])">
                            {{ __('Stock-Out') }}
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>  --}} -->
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Search..." />
                <!-- {{-- <a href="{{ route('admin.stocks.create') }}" class="bg-green-400 px-4 py-2 rounded-md text-white">
                    +Add
                </a>  --}} -->
            </div>
        </div>
    </x-slot>

    @php 
        $reqData = request()->all(); 
        unset($reqData['page']);
        $isFiltered = count($reqData);
    @endphp
    <div id="filters" class="{{ (!$isFiltered) ? 'hidden' : '' }} pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 relative text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('admin.stocks.index') }}" method="GET">
                        <h3 class="text-lg mb-4">{{ __('Filters') }}</h3>
                        <div id="close-filters" class="close-filters absolute flex items-center justify-center top-2 right-4 rounded-full h-8 w-8 shadow text-3xl cursor-pointer">
                            &times;
                        </div>
    
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <x-input-label for="product_code" :value="__('Product Code')" />
                                <x-text-input id="product_code" name="product_code" type="text" class="mt-1 block w-full" :value="request('product_code')" placeholder="{{ __('Product Code') }}"/>
                            </div>
                            <div>
                                <x-input-label for="product_name" :value="__('Product Name')" />
                                <x-text-input id="product_name" name="product_name" type="text" class="mt-1 block w-full" :value="request('product_name')" placeholder="{{ __('Product Name') }}"/>
                            </div>
                            <div>
                                <x-input-label for="color_id" :value="__('Color')" />
                                <select name="color_id" id="color_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}" @selected(request('color_id') == $color->id)>{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="size_id" :value="__('Size')" />
                                <select name="size_id" id="size_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    @foreach ($sizes as $size)
                                        <option value="{{ $size->id }}" @selected(request('size_id') == $size->id)>{{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
    
                        <div class="mb-2">
                            <x-input-label for="order_by" :value="__('Order By')" />
                            
                            <div class="grid grid-cols-3 gap-2">
                                <select name="order_by" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    <option value="stock_qty" @selected(request('order_by') == 'stock_qty') >{{ __('Stock QTY') }}</option>
                                </select>
                                <select name="order" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="asc" @selected(request('order') == 'asc')>{{ __('Ascending') }}</option>
                                    <option value="desc" @selected(request('order') == 'desc')>{{ __('Descending') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <a href="{{ route('admin.stocks.index') }}">
                                <x-secondary-button type="button">{{ __('Clear') }}</x-secondary-button>
                            </a>
                            <x-primary-button type="submit">{{ __('Search') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="pt-12 pb-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('#No.') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Image') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Product') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Color') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Size') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stock QTY') }}</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($stocks as $stock)
                                @php $product = ($stock->model instanceof \App\Models\Product) ? $stock->model : $stock->model->product @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ (request('page', 1) - 1) * $perPage + $loop->iteration }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if ($product->images->first())
                                        <div class="relative h-16 w-16 overflow-hidden">
                                            <img src="{{ $product->images->first()?->getUrl('thumb') }}" alt="" class="absolute top-0 left-0 w-full h-full" style="object-fit: contain;">
                                        </div>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $stock->model->name ?? $stock->model->product?->name }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center gap-4">
                                            <span class="w-8 h-8 rounded-md" style="background-color: {{ $stock->model->color?->color_code }};"></span>
                                            <p>{{ $stock->model->color?->name }}</p>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $stock->model->size?->name }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $stock->stock_qty }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            No record found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                    
                </div>
            </div>
        </div>
    </div>


    @if ($stocks->hasPages())
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
    @endif


    <x-slot name="script">
        <script>
            $(document).ready(function () {
                $('[name="search"]').on('focus, click', function () {
                    $(this).blur();
                    $('#filters').removeClass('hidden');
                });

                $('#close-filters').on('click', function () {
                    $(this).blur();
                    $('#filters').addClass('hidden');
                });
            });
        </script>
    </x-slot>

</x-app-layout>