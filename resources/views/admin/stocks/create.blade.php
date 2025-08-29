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

    <div id="qrCodePop" class="fixed inset-0 flex items-center justify-center z-50" style="display: none;">
        <div class="relative p-6 rounded-lg shadow-2xl z-10 bg-white dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10 max-w-lg mx-auto w-11/12 md:w-full">
            <button type="button" id="close-scanner" class="absolute top-4 right-4 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white z-50">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div id="reader" class="w-full h-auto aspect-video"></div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header class="flex items-center justify-between gap-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Create New Stock') }}
                            </h2>
                            <x-secondary-button type="button" id="scan-barcode">{{ __('SCAN BARCODE') }}</x-secondary-button>
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
                                        <option value="{{ $transaction_type->value }}" @selected($transaction_type->value == request('type'))>{{ $transaction_type->label() }}</option>
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
        <script src="{{ url('/assets/js/html5toqrcodescanner.min.js') }}"></script>
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

                class QrCodeScanner {
                    constructor(elm) {
                        this.DomElment = $(`#qrCodePop`);
                        this.elm = new Html5Qrcode(elm, { formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE] });
                        this.sizeChart = [200, 250, 300, 350];
                        this.curSizeCount = 0;
                        this.face = { facingMode: "environment", };
                        this.config = { fps: 10, qrbox: this.sizeChart[this.curSizeCount] };
                    }

                    startScanning() {
                        $(this.DomElment).css('display', 'flex');
                        this.elm.start(this.face, this.config,
                            (text, res) => {
                                $(`[name="product_id"]`).val(text);
                                if ($(`[name="product_id"]`).val() != '') {
                                    toastr.success("Product Scanned Successfully");
                                    this.stopScanning();
                                } 
                            }
                        ).then().catch(err => {
                            $(this.DomElment).hide()
                            toastr.error(err);
                        });
                    }
                    stopScanning() {
                        $(this.DomElment).hide();
                        this.elm.stop().then(ignore => { }).catch(err => { console.error(err); });
                    }
                    async changeCameraSize() {
                        await this.stopScanning();
                        this.curSizeCount++;
                        if (this.curSizeCount >= this.sizeChart.length) {
                            this.curSizeCount = 0;
                        }
                        this.config.qrbox = this.sizeChart[this.curSizeCount];
                        this.startScanning();
                    }
                    async changeCamera() {
                        await this.stopScanning();
                        this.face.facingMode = this.face.facingMode == "user" ? 'environment' : 'user';
                        this.startScanning();
                    }
                }

                let scnr = new QrCodeScanner('reader');
                $('#scan-barcode').click(function () {
                    scnr.startScanning();
                });

                $('#close-scanner').click(function () {
                    scnr.stopScanning();
                });
            });
        </script>
    </x-slot>
</x-app-layout>