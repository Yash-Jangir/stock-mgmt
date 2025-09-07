<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Purchase List') }}
            </h2>
            <div class="flex items-center justify-end gap-2">
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Search..." />
                <a href="{{ route('admin.purchases.create') }}" class="bg-green-400 px-4 py-2 rounded-md text-white">
                    +Add
                </a>
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
                    
                    <form action="{{ route('admin.purchases.index') }}" method="GET">
                        <h3 class="text-lg mb-4">{{ __('Filters') }}</h3>
                        <div id="close-filters" class="close-filters absolute flex items-center justify-center top-2 right-4 rounded-full h-8 w-8 shadow text-3xl cursor-pointer">
                            &times;
                        </div>
    
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <x-input-label for="supplier_name" :value="__('Supplier Name')" />
                                <x-text-input id="supplier_name" name="supplier_name" type="text" class="mt-1 block w-full" :value="request('supplier_name')" placeholder="{{ __('Supplier Name') }}"/>
                            </div>
                            <div>
                                <x-input-label for="slip_date_from" :value="__('Slip Date From')" />
                                <x-text-input id="slip_date_from" name="slip_date_from" type="date" class="mt-1 block w-full" :value="request('slip_date_from')" placeholder="{{ __('Slip Date From') }}"/>
                            </div>
                            <div>
                                <x-input-label for="slip_date_to" :value="__('Slip Date To')" />
                                <x-text-input id="slip_date_to" name="slip_date_to" type="date" class="mt-1 block w-full" :value="request('slip_date_to')" placeholder="{{ __('Slip Date To') }}"/>
                            </div>
                        </div>
    
                        <div class="mb-2">
                            <x-input-label for="order_by" :value="__('Order By')" />
                            
                            <div class="grid grid-cols-3 gap-2">
                                <select name="order_by" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    <option value="client_name" @selected(request('order_by') == 'client_name') >{{ __('Supplier Name') }}</option>
                                    <option value="slip_date" @selected(request('order_by') == 'slip_date') >{{ __('Slip Date') }}</option>
                                </select>
                                <select name="order" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="asc" @selected(request('order') == 'asc')>{{ __('Ascending') }}</option>
                                    <option value="desc" @selected(request('order') == 'desc')>{{ __('Descending') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <a href="{{ route('admin.purchases.index') }}">
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
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Slip No') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Slip Date') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Supplier Name') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Total Price') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Discount') }}</th>
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">&nbsp;</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ (request('page', 1) - 1) * $perPage + $loop->iteration }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $purchase->slip_no }}
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ date('Y/m/d', strtotime($purchase->slip_date)) }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $purchase->client_name }}
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">₹{{ number_format($purchase->total_price) }}</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $purchase->discount }}%</td>
                                    <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center justify-end gap-4">
                                            <!-- {{-- <a href="{{ route('admin.purchases.index', ['export' => 'pdf', 'id' => $purchase->id]) }}" target="_blank">
                                                <x-secondary-button type="button">{{ __('PDF') }}</x-secondary-button>
                                            </a>  --}} -->
                                            <a href="{{ route('admin.purchases.show', $purchase->id) }}">
                                                <x-secondary-button type="button">{{ __('Detail') }}</x-secondary-button>
                                            </a>
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


    @if ($purchases->hasPages())
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ $purchases->links() }}
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