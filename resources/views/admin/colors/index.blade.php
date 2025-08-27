<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <a href="{{ route('admin.masters') }}">{{ __('Masters') }}</a> > {{ __('Color') }}
            </h2>
            <div class="flex items-center justify-end gap-2">
                <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" placeholder="Search..." />
                <span></span>
                <a href="{{ route('admin.colors.create') }}" class="bg-green-400 px-4 py-2 rounded-md text-white">
                    +Add
                </a>
            </div>
        </div>
    </x-slot>

    <div id="filters" class="{{ (!array_filter(request()->all())) ? 'hidden' : '' }} pt-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 relative text-gray-900 dark:text-gray-100">
                    
                    <form action="{{ route('admin.colors.index') }}" method="GET">
                        <h3 class="text-lg mb-4">{{ __('Filters') }}</h3>
                        <div id="close-filters" class="close-filters absolute flex items-center justify-center top-2 right-4 rounded-full h-8 w-8 shadow text-3xl cursor-pointer">
                            &times;
                        </div>
    
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <x-input-label for="code" :value="__('Color Code')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="request('code')" placeholder="{{ __('Color Code') }}"/>
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Color Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="request('name')" placeholder="{{ __('Color Name') }}"/>
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
                                    <option value="code" @selected(request('order_by') == 'code') >{{ __('Color Code') }}</option>
                                    <option value="name" @selected(request('order_by') == 'name') >{{ __('Color Name') }}</option>
                                    <option value="rank" @selected(request('order_by') == 'rank') >{{ __('Rank') }}</option>
                                </select>
                                <select name="order" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="asc" @selected(request('order') == 'asc')>{{ __('Ascending') }}</option>
                                    <option value="desc" @selected(request('order') == 'desc')>{{ __('Descending') }}</option>
                                </select>
                            </div>
                        </div>
    
                        <div class="flex items-center justify-center gap-4 mt-6">
                            <a href="{{ route('admin.colors.index') }}">
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Color Code') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Color Name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Rank') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">

                            @forelse ($colors as $color)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ (request('page', 1) - 1) * $perPage + $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <div class="flex items-center gap-4">
                                            <span class="w-8 h-8 rounded-md" style="background-color: {{ $color->color_code }}"></span>
                                            <p>{{ $color->code }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $color->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $color->rank }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($color->is_active)
                                            <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                                        @else
                                            <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">In-active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('admin.colors.edit', $color->id) }}">
                                                <x-secondary-button type="button">{{ __('Edit') }}</x-secondary-button>
                                            </a>
                                            <form action="{{ route('admin.colors.destroy', $color->id) }}" onsubmit="return confirm('Are you sure?');" method="post">
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


    @if ($colors->hasPages())
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ $colors->links() }}
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