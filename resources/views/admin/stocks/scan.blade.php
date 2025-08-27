<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <a href="{{ route('admin.stocks.index') }}">{{ __('Stock') }}</a> > {{ __('Create') }}
        </h2>
    </x-slot>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="toast-err absolute z-[9] top-0 left-1/2 transform -translate-x-1/2 mt-4 px-4 py-2 bg-red-500 text-white rounded shadow-lg">
                <div class="flex items-center justify-between gap-4">
                    <span>{{ $error }}</span>
                    <button onclick="this.closest('.toast-err').remove()" class="text-white hover:text-gray-200">&times;</button>
                </div>
            </div>
        @endforeach
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    
                    <section>
                        <header class="flex gap-4 items-center">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ $type === 'in' ? __('Register Stock-In') : __('Register Stock-Out') }}
                            </h2>
                            <x-secondary-button type="button" id="scan-barcode">{{ __('SCAN BARCODE') }}</x-secondary-button>
                        </header>

                        <form method="post" action="{{ route('admin.stocks.scan', $type) }}" onsubmit="return false;" class="mt-6 space-y-6">
                            @csrf

                            <div id="stock_qty">
                                <x-input-label for="stock_qty" :value="__('Stock QTY')" />
                                <x-text-input id="stock_qty" name="stock_qty" type="number" class="mt-1 block w-full" :value="old('stock_qty')" placeholder="{{ __('Stock QTY') }}" readonly required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock_qty')" />
                                <input type="hidden" name="storeScannedSku" value="">
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.stocks.index') }}">
                                    <x-secondary-button>{{ __('Cancel') }}</x-secondary-button>
                                </a>
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </section>

                
                </div>
            </div>

        </div>
    </div>

    <x-slot name="script">
        <script>
            $(document).ready(function() {
                const toastr = {
                    error: (message) => {
                        $('body').append(`<div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="toast-err absolute z-[9] top-0 left-1/2 transform -translate-x-1/2 mt-4 px-4 py-2 bg-red-500 text-white rounded shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <span>[message]</span>
                                <button onclick="this.closest('.toast-err').remove()" class="text-white hover:text-gray-200">&times;</button>
                            </div>
                        </div>`.replace('[message]', message));
                    },
                    success: (message) => {
                        $('body').append(`<div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="toast-success absolute z-[9] top-0 left-1/2 transform -translate-x-1/2 mt-4 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <span>[message]</span>
                                <button onclick="this.closest('.toast-success').remove()" class="text-white hover:text-gray-200">&times;</button>
                            </div>
                        </div>` .replace('[message]', message));
                    }
                };

                // toastr.success('Successfully scanned');
                // toastr.error('Failed to scan');
            });
        </script>
    </x-slot>
</x-app-layout>