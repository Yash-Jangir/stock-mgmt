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
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Create New Stock') }}
                            </h2>
                        </header>

                        <form method="post" action="{{ route('admin.stocks.store') }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="product_id" :value="__('Product')" />
                                <select name="product_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value=""></option>
                                    @foreach ($products as $product)
                                        @forelse($product->skus as $sku)
                                            <option value="p:{{ $product->id }}|s:{{ $sku->id }}">{{ $product->name }} - [{{ $sku->color->name }}] - [{{ $sku->size->name }}]</option>
                                        @empty
                                            <option value="p:{{ $product->id }}|p:{{ $product->id }}">{{ $product->name }}</option>
                                        @endforelse
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                            </div>

                            <div>
                                <x-input-label for="type" :value="__('Transaction Type')" />
                                <select name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value=""></option>
                                    @foreach ($transaction_types as $transaction_type)
                                        <option value="{{ $transaction_type->value }}">{{ $transaction_type->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('type')" />
                            </div>

                            <div>
                                <x-input-label for="stock_qty" :value="__('Stock QTY')" />
                                <x-text-input id="stock_qty" name="stock_qty" type="number" class="mt-1 block w-full" :value="old('stock_qty')" placeholder="{{ __('Stock QTY') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock_qty')" />
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

            });
        </script>
    </x-slot>
</x-app-layout>