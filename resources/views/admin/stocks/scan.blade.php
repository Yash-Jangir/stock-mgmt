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
                    </section>

                </div>
            </div>

        </div>
    </div>

    <div id="qrCodePop" class="fixed top-0 left-0 w-full h-full" style="display: none;">
        <x-secondary-button type="button" id="close-scanner" class="absolute top-4 right-4 z-50">{{ __('Close Scanner') }}</x-secondary-button>
        <div id="reader"></div>
    </div>

    <div class="fixed top-0 left-0 w-full h-full z-50 hidden" id="scan-barcode-modal">
        <form method="post" action="{{ route('admin.stocks.store') }}" onsubmit="return false;" class="mt-6 space-y-6">
            @csrf

            <div id="stock_qty">
                <x-input-label for="stock_qty" :value="__('Stock QTY')" />
                <x-text-input id="stock_qty" name="stock_qty" type="number" class="mt-1 block w-full" :value="old('stock_qty')" placeholder="{{ __('Stock QTY') }}" readonly required />
                <x-input-error class="mt-2" :messages="$errors->get('stock_qty')" />
                <input type="hidden" name="product_id" value="">
                <input type="hidden" name="type" value="{{ $type }}">
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.stocks.index') }}">
                    <x-secondary-button>{{ __('Cancel') }}</x-secondary-button>
                </a>
                <x-primary-button>{{ __('Save') }}</x-primary-button>
            </div>
        </form>
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
                        this.elm = new Html5Qrcode(elm, { formatsToSupport: [Html5QrcodeSupportedFormats.CODE_128] });
                        this.sizeChart = [200, 250, 300, 350];
                        this.curSizeCount = 0;
                        this.face = { facingMode: "environment", };
                        this.config = { fps: 10, qrbox: this.sizeChart[this.curSizeCount] };
                        this.alreadyCalled = false;
                        this.productPicked = [];
                        this.lastAjaxCall = 0;
                        this.pausePeriod = 5000;
                        this.oldDecode = '';
                    }

                    startScanning() {
                        $(this.DomElment).css('display', 'block');
                        this.elm.start(this.face, this.config,
                            (text, res) => {
                                toastr.success(text);
                                if (this.alreadyCalled || (this.oldDecode == text && (new Date()).getTime() < this.lastAjaxCall)) return;

                                this.alreadyCalled = true;
                                this.oldDecode     = text;
                                this.lastAjaxCall  = ((new Date()).getTime() + this.pausePeriod);
                                this.elm.pause();
                                this.makeAjaxCall(text);
                            }
                        ).then().catch(err => {
                            $(this.DomElment).hide()
                            console.log(err)
                        });
                    }
                    stopScanning() {
                        // if (this.elm.getState() == 2) {
                        $(this.DomElment).hide();
                        this.elm.stop().then(ignore => { }).catch(err => { console.error(err); });
                        // }
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
                    makeAjaxCall(text) {
                        const pid = String(text).split('-')[2];
                        if (this.productPicked.includes(pid)) {
                        toastr.error("{{ trans('cruds.product_already_exits') }}");
                        this.alreadyCalled = false;
                        this.elm.resume();
                        return;
                        }
                        $.get('{{ route("admin.stocks.scanResult") }}', { id: pid })
                        .done((resp) => {
                            if (resp.success) {
                            const data = resp.data;

                            if (data.status == 'available') {
                                this.productPicked.push(pid);
                                var tostarMa = resp.data.toastM;
                                
                                $('#product-list-wrapper').append(data.html);
                                toastr.success(tostarMa);

                            } else {
                                // openSampleLendingStatusPop(data);
                                toastr.error("{{ trans('cruds.item_is_not_returned_please_do_it_again_after_returned') }}".replace('\n', '<br>'));
                            }

                            } else {
                            toastr.error('{{ trans("cruds.we_could_not_find_product") }}');
                            }
                            
                            this.alreadyCalled = false;
                            this.elm.resume();
                        })
                        .fail((resp) => {
                            toastr.error('{{ trans("cruds.we_could_not_find_product") }}');
                            this.alreadyCalled = false;
                            this.oldDecode = '';
                            this.elm.resume();
                        });
                    }
                }

                let scnr = new QrCodeScanner('reader');
                $('#scan-barcode').click(function () {
                    toastr.success("{{ trans('cruds.scan_barcode') }}");
                    scnr.startScanning();
                });

                $('#close-scanner').click(function () {
                    scnr.stopScanning();
                });
            });
        </script>
    </x-slot>
</x-app-layout>