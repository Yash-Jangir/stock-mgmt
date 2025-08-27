<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Product') }}
            </h2>
            <div class="flex items-center justify-end gap-2">
                <x-secondary-button type="button" id="gen-barcode">{{ __('Barcode') }}</x-secondary-button>
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Search..." />
                <a href="{{ route('admin.products.create') }}" class="bg-green-400 px-4 py-2 rounded-md text-white">
                    +Add
                </a>
            </div>
        </div>
    </x-slot>

    <div id="filters" class="{{ (!array_filter(request()->all())) ? 'hidden' : '' }} pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 relative text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('admin.products.index') }}" method="GET">
                        <h3 class="text-lg mb-4">{{ __('Filters') }}</h3>
                        <div id="close-filters" class="close-filters absolute flex items-center justify-center top-2 right-4 rounded-full h-8 w-8 shadow text-3xl cursor-pointer">
                            &times;
                        </div>
    
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <x-input-label for="code" :value="__('Product Code')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="request('code')" placeholder="{{ __('Product Code') }}"/>
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Product Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="request('name')" placeholder="{{ __('Product Name') }}"/>
                            </div>
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select name="is_active" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    <option value="1" @selected(request('is_active') == 1)>{{ __('Active') }}</option>
                                    <option value="0" @selected(request('is_active') == '0')>{{ __('In-active') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="mb-2">
                            <x-input-label for="order_by" :value="__('Order By')" />
                            
                            <div class="grid grid-cols-3 gap-2">
                                <select name="order_by" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    <option value="code" @selected(request('order_by') == 'code') >{{ __('Product Code') }}</option>
                                    <option value="name" @selected(request('order_by') == 'name') >{{ __('Product Name') }}</option>
                                    <option value="price" @selected(request('order_by') == 'price') >{{ __('Price') }}</option>
                                </select>
                                <select name="order" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="asc" @selected(request('order') == 'asc')>{{ __('Ascending') }}</option>
                                    <option value="desc" @selected(request('order') == 'desc')>{{ __('Descending') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <a href="{{ route('admin.products.index') }}">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <x-text-input type="checkbox" name="select_all" id="select_all" />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('#No.') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Product Code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Product Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Price') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">

                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <x-text-input type="checkbox" class="select" value="{{ $product->id }}" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ (request('page', 1) - 1) * $perPage + $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $product->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($product->price) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($product->is_active)
                                            <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                                        @else
                                            <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">In-active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('admin.products.edit', $product->id) }}">
                                                <x-secondary-button type="button">{{ __('Edit') }}</x-secondary-button>
                                            </a>
                                            <form action="{{ route('admin.products.destroy', $product->id) }}" onsubmit="return confirm('Are you sure?');" method="post">
                                                @method('delete')
                                                @csrf
                                                <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                            </form>
                                        </div>
                                    </td>
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


    @if ($products->hasPages())
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ $products->links() }}
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

                $('#select_all').click(function () {
                    $('.select').prop('checked', this.checked);
                });

                $('.select').click(function () {
                    $('#select_all').prop('checked', $('.select').length == $('.select:checked').length);
                });

                $('#gen-barcode').click(function () {
                    if (!$('.select:checked').length) return alert('Please select product');

                    const url = new URL("{!! url()->full() !!}");
                    url.searchParams.set('export', 'barcode');
                    url.searchParams.set('ids', $('.select:checked').toArray().map(i => $(i).val()).join(','));

                    window.open(url.toString(), '_blank');
                });
            });
        </script>
    </x-slot>

</x-app-layout>