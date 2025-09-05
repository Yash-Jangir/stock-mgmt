<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <a href="{{ route('admin.purchases.index') }}">{{ __('Purchase List') }}</a> > {{ __('Create') }}
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
                            {{ __('Supplier Details') }}
                        </h2>
                        <x-secondary-button type="button" id="scan-qrcode">{{ __('SCAN QRCODE') }}</x-secondary-button>
                    </header>

                    <form id="stock-form" method="post" action="{{ route('admin.purchases.store') }}" class="mt-6">
                        @csrf

                        <div id="supplier-details">
                            <div class="grid grid-cols-3 gap-4 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="slip_no" :value="__('Slip No.')" />
                                        <x-text-input id="slip_no" name="slip_no" class="mt-1 block grow" value="{{ $slip_no }}" readonly />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('slip_no')" />
                                </div>
                                <div></div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="slip_date" :value="__('Slip Date')" />
                                        <x-text-input id="slip_date" name="slip_date" class="mt-1 block grow" value="{{ date('Y/m/d') }}" readonly />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('slip_date')" />
                                </div>
                            </div>
    
                            <div class="grid grid-cols-2 gap-4 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="supplier_name" :value="__('Supplier Name')" />
                                        <x-text-input id="supplier_name" name="supplier_name" class="mt-1 block grow" value="" required />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('supplier_name')" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="gst_number" :value="__('GST Number')" />
                                        <x-text-input id="gst_number" name="gst_number" class="mt-1 block grow" value="" />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('gst_number')" />
                                </div>
                            </div>
    
                            <div class="grid grid-cols-1 gap-4 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="address" :value="__('Address')" />
                                        <x-text-input id="address" name="address" class="mt-1 block grow" value="" required />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="contact_no" :value="__('Contact No.')" />
                                        <x-text-input id="contact_no" name="contact_no" class="mt-1 block grow" value="" required />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_no')" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="email" :value="__('Email')" />
                                        <x-text-input id="email" name="email" class="mt-1 block grow" value="" />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>
                        </div>


                        <div class="mt-8">
                            <div class="grid grid-cols-3 gap-4 mb-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="total_price" :value="__('Total Price')" />
                                        <x-text-input id="total_price" type="number" name="total_price" class="mt-1 block grow" value="" readonly />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('total_price')" />
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <x-input-label class="w-[100px]" for="discount" :value="__('Discount')" />
                                        <x-text-input id="discount" type="number" name="discount" class="mt-1 block grow" value="" />
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('discount')" />
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-100 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Product') }}</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Unit Price') }}</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('QTY') }}</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Price') }}</th>
                                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <button type="button" id="add-row" class="bg-green-400 px-4 py-2 font-semibold text-sm uppercase tracking-widest rounded-md text-white">+Add</button>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="row-wrapper" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <select name="product_id[]" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                                <option value=""></option>
                                                @foreach ($products as $product)
                                                    @forelse($product->skus as $sku)
                                                        <option 
                                                            value="p:{{ $product->id }}|s:{{ $sku->id }}" 
                                                            data-stock="{{ $sku->stock?->stock_qty }}"
                                                            data-price="{{ (int) $sku->price }}"
                                                        >{{ $product->name }} - [{{ $sku->color->name }}] - [{{ $sku->size->name }}]</option>
                                                    @empty
                                                        <option 
                                                            value="p:{{ $product->id }}|p:{{ $product->id }}" 
                                                            data-stock="{{ $product->stock?->stock_qty }}"
                                                            data-price="{{ (int) $product->price }}"
                                                        >{{ $product->name }}</option>
                                                    @endforelse
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <x-text-input id="unit_price" name="unit_price[]" type="number" class="mt-1 block w-full" placeholder="{{ __('Unit Price') }}" required />
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <x-text-input id="qty" name="qty[]" type="number" class="mt-1 block w-full" placeholder="{{ __('QTY') }}" required />
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <x-text-input id="price" name="price[]" type="number" class="mt-1 block w-full" placeholder="{{ __('Price') }}" readonly />
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <x-danger-button class="rm-btn" type="button" style="display: none;">&times;</x-danger-button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <div class="flex items-center justify-end gap-4 mt-8">
                            <a href="{{ route('admin.purchases.index') }}">
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
                const toggleRemoveBtn = () => {
                    $('.rm-btn')[$('#row-wrapper tr').length <= 1 ? 'hide' : 'show']();
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
                            $(this).closest('tr').css({'background': 'tomato'}).remove();
                        }
                    });

                    $('[name="qty[]"]').each(function () {
                        if (typeOut) {
                            const product    = $(this).closest('tr').find('[name="product_id[]"]').val();
                            const availStock = +$(this).closest('tr').find(`[name="product_id[]"]`).find(`[value="${product}"]`).data('stock');

                            if (availStock) {
                                if (+$(this).val() > availStock) {
                                    $(this).val(availStock);
                                }
                            }
                        }

                        $(this).val(Math.abs($(this).val()));
                    });

                    if (!$('[name="product_id[]"]').length) {
                        toastr.error("Please add at least one product.");
                        $('#add-row').click();
                    }

                    let isInvalidData   = false;
                    let firstInvalidElm = null;
                    form.find('[required]').each(function () {
                        if ($(this).attr('type') === 'number') {
                            if (!strtonum($(this).val())) {
                                firstInvalidElm = firstInvalidElm || $(this);
                                isInvalidData   = true;
                            }

                        } else if (!$(this).val()) {
                            firstInvalidElm = firstInvalidElm || $(this);
                            isInvalidData = true;
                        }
                    });

                    if (isInvalidData) {
                        firstInvalidElm.focus();
                        return false;
                    }

                    form[0].submit();
                }

                const strtonum = str => (isNaN(+String(str)) ? 0 : +str);

                const calculateTotal = () => {
                    let tp = $('[name="price[]"]').toArray().reduce((a, b) => strtonum(a) + strtonum($(b).val()), 0);
                    let di = strtonum($('[name="discount"]').val());

                    tp -= ((tp * di) / 100);

                    $('[name="total_price"]').val(tp);
                };

                const calculatePrice = (row) => {
                    let unit_price = strtonum(row.find('[name="unit_price[]"]').val());
                    let qty        = strtonum(row.find('[name="qty[]"]').val());

                    row.find('[name="price[]"]').val(qty * unit_price);

                    calculateTotal();
                };

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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <select name="product_id[]" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value=""></option>
                                    @foreach ($products as $product)
                                        @forelse($product->skus as $sku)
                                            <option 
                                                value="p:{{ $product->id }}|s:{{ $sku->id }}" 
                                                data-stock="{{ $sku->stock?->stock_qty }}"
                                                data-price="{{ (int) $sku->price }}"
                                            >{{ $product->name }} - [{{ $sku->color->name }}] - [{{ $sku->size->name }}]</option>
                                        @empty
                                            <option 
                                                value="p:{{ $product->id }}|p:{{ $product->id }}" 
                                                data-stock="{{ $product->stock?->stock_qty }}"
                                                data-price="{{ (int) $product->price }}"
                                            >{{ $product->name }}</option>
                                        @endforelse
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <x-text-input id="unit_price" name="unit_price[]" type="number" class="mt-1 block w-full" placeholder="{{ __('Unit Price') }}" required />
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <x-text-input id="qty" name="qty[]" type="number" class="mt-1 block w-full" placeholder="{{ __('QTY') }}" required />
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <x-text-input id="price" name="price[]" type="number" class="mt-1 block w-full" placeholder="{{ __('Price') }}" readonly />
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <x-danger-button class="rm-btn" type="button" style="display: none;">&times;</x-danger-button>
                            </td>
                        </tr>
                    `);

                    toggleRemoveBtn();
                });

                $('body').on('click', '.rm-btn', function () {
                    $(this).closest('tr').remove();
                    toggleRemoveBtn();
                });

                $('body').on('change', '[name="product_id[]"]', function () {
                    const value   = $(this).val().trim();
                    const isExist = $('[name="product_id[]"]').not($(this)).toArray().some(input => $(input).val().trim() == value && value);
                    const tr      = $(this).closest('tr');
                    const price   = tr.find(`[name="product_id[]"]`).find(`[value="${value}"]`).data('price');

                    if (isExist) {
                        $(this).val('');
                        toastr.error("Product already picked.");
                    } else {
                        tr.find('[name="unit_price[]"]').val(price);
                        tr.find('[name="qty[]"]').change();
                    }

                    if (allProductFieldSelected()) $('#add-row').click();
                });

                $('body').on('change', '[name="qty[]"]', function () {
                    const typeOut  = $('[name="type"]').val() === 'out';
                    const row      = $(this).closest('tr');
                    const product  = row.find('[name="product_id[]"]').val();
                    let unit_price = strtonum(row.find('[name="unit_price[]"]').val());
                    let qty        = strtonum($(this).val());

                    if (qty < 0) {
                        toastr.error("Stock QTY can't be negative.");
                        qty = Math.abs(qty);
                        $(this).val(qty);
                    }

                    if (typeOut && qty && product) {
                        let stock = row.find(`[name="product_id[]"]`).find(`[value="${product}"]`).data('stock');
                        stock     = strtonum(stock);
                        if (qty > stock) {
                            $(this).val(stock);
                            toastr.error(`Can't pick more than available stock: ${stock}.`);
                        }
                    }

                    row.find('[name="price[]"]').val(qty * unit_price);

                    calculatePrice(row);
                });

                $('body').on('change', '[name="unit_price[]"]', function () {
                    const row = $(this).closest('tr');

                    calculatePrice(row);                    
                });

                $('[name="discount"]').on('input', function (e) {
                    if (strtonum($(this).val()) >= 100) {
                        e.preventDefault();
                        $(this).val($(this).val().slice(0, 2));
                    }
                    calculateTotal();
                });

                $('#stock-form').submit(function (e) {
                    e.preventDefault();
                    
                    formSubmition($(this));
                });

                $('#stock-form').submit(function (e) {
                    e.preventDefault();
                    
                    formSubmition(this);
                });

                $('#save-stock').click(function (e) {
                    e.preventDefault();
                    
                    formSubmition($(this).closest('form'));
                });

                toggleRemoveBtn();
            });
        </script>
    </x-slot>
</x-app-layout>