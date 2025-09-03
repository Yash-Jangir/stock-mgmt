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
                <section>
                    <header class="flex items-center justify-between gap-4 mb-4">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Create New Stock') }}
                        </h2>
                        <x-secondary-button type="button" id="scan-qrcode">{{ __('SCAN QRCODE') }}</x-secondary-button>
                    </header>

                    <form id="stock-form" method="post" action="{{ route('admin.stocks.store') }}" class="mt-6 space-y-6">
                        @csrf

                        <div class="w-2/3 grid grid-cols-2 gap-2">
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

                            <div class="flex items-end justify-end">
                                <button type="button" id="add-row" class="bg-green-400 px-4 py-2 font-semibold text-sm uppercase tracking-widest rounded-md text-white">+Add Row</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <x-input-label for="product_id" :value="__('Product')" />
                            </div>
    
                            <div>
                                <x-input-label for="stock_qty[]" :value="__('Stock QTY')" />
                            </div>
                        </div>
                        
                        <div id="row-wrapper">
                            <div class="prd-row grid grid-cols-3 gap-2 mb-2">
                                <div>
                                    <select name="product_id[]" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value=""></option>
                                        @foreach ($products as $product)
                                            @forelse($product->skus as $sku)
                                                <option value="p:{{ $product->id }}|s:{{ $sku->id }}" data-stock="{{ $sku->stock?->stock_qty }}">{{ $product->name }} - [{{ $sku->color->name }}] - [{{ $sku->size->name }}]</option>
                                            @empty
                                                <option value="p:{{ $product->id }}|p:{{ $product->id }}" data-stock="{{ $product->stock?->stock_qty }}">{{ $product->name }}</option>
                                            @endforelse
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                                </div>

                                <div>
                                    <x-text-input id="stock_qty" name="stock_qty[]" type="number" class="mt-1 block w-full" :value="old('stock_qty')" placeholder="{{ __('Stock QTY') }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('stock_qty')" />
                                </div>

                                <div class="flex items-end mb-1">
                                    <x-danger-button class="rm-btn" type="button" style="display: none;">{{ __('Delete') }}</x-danger-button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <a href="{{ route('admin.stocks.index') }}">
                                <x-secondary-button>{{ __('Cancel') }}</x-secondary-button>
                            </a>
                            <x-primary-button type="button" id="save-stock">{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script src="{{ url('/assets/js/html5toqrcodescanner.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                const toastr = {
                    error: (message) => {
                        $('#toastr-wrapper').append(`<div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="toast-err mt-4 px-4 py-2 bg-red-500 text-white rounded shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <span>[message]</span>
                                <button onclick="this.closest('.toast-err').remove()" class="text-white hover:text-gray-200">&times;</button>
                            </div>
                        </div>`.replace('[message]', message));
                    },
                    success: (message) => {
                        $('#toastr-wrapper').append(`<div
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="toast-success mt-4 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
                            <div class="flex items-center justify-between gap-4">
                                <span>[message]</span>
                                <button onclick="this.closest('.toast-success').remove()" class="text-white hover:text-gray-200">&times;</button>
                            </div>
                        </div>` .replace('[message]', message));
                    }
                };

                const toggleRemoveBtn = () => {
                    $('.rm-btn')[$('#row-wrapper .prd-row').length <= 1 ? 'hide' : 'show']();
                };

                const allProductFieldSelected = () => $('[name="product_id[]"]').toArray().every(input => input.value != '');

                const getLastEmptyProductField = () => {
                    const lastEmptyField = $(`[name="product_id[]"]`).toArray().reverse().find(input => input.value != '').next();
                    if (lastEmptyField.length) {
                        return lastEmptyField;
                    }

                    $('#add-row').click();
                    return $(`[name="product_id[]"]`).last();
                };

                const formSubmition = (form) => {
                    const typeOut = $('[name="type"]').val() === 'out';
                    $('[name="product_id[]"]').each(function () {
                        if (!$(this).val()) {
                            $(this).closest('.prd-row').css({'background': 'tomato'}).remove();
                        }
                    });

                    $('[name="stock_qty[]"]').each(function () {
                        if (typeOut) {
                            const product    = $(this).closest('.prd-row').find('[name="product_id[]"]').val();
                            const availStock = +$(this).closest('.prd-row').find(`[name="product_id[]"]`).find(`[value="${product}"]`).data('stock');

                            if (availStock) {
                                if (+$(this).val() > availStock) {
                                    $(this).val(availStock);
                                }
                            }
                        }

                        if (!$(this).val() || !+$(this).val()) {
                            $(this).closest('.prd-row').css({'background': 'tomato'}).remove();
                        }
                        $(this).val(Math.abs($(this).val()));
                    });

                    if ($('[name="product_id[]"]').length) {
                        form.submit();
                    } else {
                        toastr.error("Please add at least one product.");
                        $('#add-row').click();
                    }
                }

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
                                const field = getLastEmptyProductField();
                                field.val(text).change();

                                if (field.val() != '') {
                                    toastr.success("Product Scanned Successfully");
                                    // this.stopScanning();
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
                $('#scan-qrcode').click(function () {
                    scnr.startScanning();
                });

                $('#close-scanner').click(function () {
                    scnr.stopScanning();
                });

                $('#add-row').click(function () {
                    $('#row-wrapper').append(`
                        <div class="prd-row grid grid-cols-3 gap-2 mb-2">
                            <div>
                                <select name="product_id[]" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value=""></option>
                                    @foreach ($products as $product)
                                        @forelse($product->skus as $sku)
                                            <option value="p:{{ $product->id }}|s:{{ $sku->id }}" data-stock="{{ $sku->stock?->stock_qty }}">{{ $product->name }} - [{{ $sku->color->name }}] - [{{ $sku->size->name }}]</option>
                                        @empty
                                            <option value="p:{{ $product->id }}|p:{{ $product->id }}" data-stock="{{ $product->stock?->stock_qty }}">{{ $product->name }}</option>
                                        @endforelse
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                            </div>

                            <div>
                                <x-text-input id="stock_qty" name="stock_qty[]" type="number" class="mt-1 block w-full" :value="old('stock_qty')" placeholder="{{ __('Stock QTY') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock_qty')" />
                            </div>

                            <div class="rm-btn flex items-end mb-1">
                                <x-danger-button type="button">{{ __('Delete') }}</x-danger-button>
                            </div>
                        </div>
                    `);

                    toggleRemoveBtn();
                });

                $('body').on('click', '.rm-btn', function () {
                    $(this).closest('.prd-row').remove();
                    toggleRemoveBtn();
                });

                $('body').on('change', '[name="product_id[]"]', function () {
                    const value   = $(this).val().trim();
                    const isExist = $('[name="product_id[]"]').not($(this)).toArray().some(input => $(input).val().trim() == value && value);

                    if (isExist) {
                        $(this).val('');
                        toastr.error("Product already picked.");
                    } else {
                        $(this).closest('.prd-row').find('[name="stock_qty[]"]').change();
                    }

                    if (allProductFieldSelected()) $('#add-row').click();
                });

                $('body').on('change', '[name="stock_qty[]"]', function () {
                    const typeOut = $('[name="type"]').val() === 'out';
                    const row     = $(this).closest('.prd-row');
                    const product = row.find('[name="product_id[]"]').val();
                    let qty       = isNaN(+$(this).val()) ? 0 : +$(this).val();

                    if (qty < 0) {
                        toastr.error("Stock QTY can't be negative.");
                        qty = Math.abs(qty);
                        $(this).val(qty);
                    }

                    if (typeOut && qty && product) {
                        let stock = row.find(`[name="product_id[]"]`).find(`[value="${product}"]`).data('stock');
                        stock     = isNaN(+stock) ? 0 : +stock;
                        if (qty > stock) {
                            $(this).val(stock);
                            toastr.error(`Can't pick more than available stock: ${stock}.`);
                        }
                    }
                });

                $('[name="type"]').change(function () {
                    $('[name="product_id[]"]').change();
                });

                $('#stock-form').submit(function (e) {
                    e.preventDefault();
                    
                    formSubmition(this);
                });

                $('#stock-form').submit(function (e) {
                    e.preventDefault();
                    
                    formSubmition(this);
                });

                $('#save-stock').click(function (e) {
                    e.preventDefault();
                    
                    formSubmition($(this).closest('form')[0]);
                });

                toggleRemoveBtn();
            });
        </script>
    </x-slot>
</x-app-layout>