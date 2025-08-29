<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Stock Transaction History') }}
            </h2>
            <div class="flex items-center justify-end gap-2">
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Search..." />
            </div>
        </div>
    </x-slot>

    <div id="filters" class="{{ (!array_filter(request()->all())) ? 'hidden' : '' }} pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 relative text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('admin.stock-transactions.index') }}" method="GET">
                        <h3 class="text-lg mb-4">{{ __('Filters') }}</h3>
                        <div id="close-filters" class="close-filters absolute flex items-center justify-center top-2 right-4 rounded-full h-8 w-8 shadow text-3xl cursor-pointer">
                            &times;
                        </div>
    
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <x-input-label for="code" :value="__('Product Code')" />
                                <x-text-input id="product_code" name="product_code" type="text" class="mt-1 block w-full" :value="request('product_code')" placeholder="{{ __('Product Code') }}"/>
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Product Name')" />
                                <x-text-input id="product_name" name="product_name" type="text" class="mt-1 block w-full" :value="request('product_name')" placeholder="{{ __('Product Name') }}"/>
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Stock Type')" />
                                <select name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    <option value="in" @selected(request('type') == 'in')>{{ __('Stock-In') }}</option>
                                    <option value="out" @selected(request('type') == 'out')>{{ __('Stock-Out') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="mb-2">
                            <x-input-label for="order_by" :value="__('Order By')" />
                            
                            <div class="grid grid-cols-3 gap-2">
                                <select name="order_by" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    <option value="created_at" @selected(request('order_by') == 'created_at') >{{ __('Transaction Date') }}</option>
                                </select>
                                <select name="order" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="asc" @selected(request('order') == 'asc')>{{ __('Ascending') }}</option>
                                    <option value="desc" @selected(request('order') == 'desc')>{{ __('Descending') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <a href="{{ route('admin.stock-transactions.index') }}">
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
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('#No.') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Product') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Color') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Size') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Stock QTY') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Transaction Date') }}</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">

                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ (request('page', 1) - 1) * $perPage + $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $transaction->model->name ?? $transaction->model->product?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->model->color?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->model->size?->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->type == 'in' ? 'Stock In' : 'Stock Out' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->stock_qty }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M d, Y, h:i A') }}</td>
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


    @if ($transactions->hasPages())
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ $transactions->links() }}
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