<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="{{ asset('/assets/css/fonts.css') }}" rel="stylesheet" />
    </head>
    <body class="font-sans antialiased">

        {{-- <div id="supplier-details">
            <div class="grid grid-cols-3 gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="slip_no" :value="__('Slip No.')" />
                        <x-text-input id="slip_no" name="slip_no" class="mt-1 block grow" value="{{ $billing->slip_no }}" readonly />
                    </div>
                </div>
                <div></div>
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="slip_date" :value="__('Slip Date')" />
                        <x-text-input id="slip_date" name="slip_date" class="mt-1 block grow" value="{{ date('Y/m/d', strtotime($billing->slip_date)) }}" readonly />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="supplier_name" :value="__('Supplier Name')" />
                        <x-text-input id="supplier_name" name="supplier_name" class="mt-1 block grow" value="{{ $billing->client_name }}" readonly required />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="gst_number" :value="__('GST Number')" />
                        <x-text-input id="gst_number" name="gst_number" class="mt-1 block grow" value="{{ $billing->gst_number }}" readonly />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="address" :value="__('Address')" />
                        <x-text-input id="address" name="address" class="mt-1 block grow" value="{{ $billing->address }}" required readonly />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="contact_no" :value="__('Contact No.')" />
                        <x-text-input id="contact_no" name="contact_no" class="mt-1 block grow" value="{{ $billing->contact_no }}" readonly required />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" class="mt-1 block grow" value="{{ $billing->email }}" readonly />
                    </div>
                </div>
            </div>
        </div>


        <div class="mt-8">
            <div class="grid grid-cols-3 gap-4 mb-2">
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="total_price" :value="__('Total Price')" />
                        <x-text-input id="total_price" type="text" name="total_price" class="mt-1 block grow" value="₹{{ number_format($billing->total_price) }}" readonly />
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <x-input-label class="w-[100px]" for="discount" :value="__('Discount')" />
                        <x-text-input id="discount" type="text" name="discount" class="mt-1 block grow" value="{{ $billing->discount }}%" readonly />
                    </div>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Product') }}</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Unit Price') }}</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('QTY') }}</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Price') }}</th>
                    </tr>
                </thead>

                <tbody id="row-wrapper" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($details as $detail)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                @php
                                    $product = @$products[$detail->product_id];
                                    $sku     = @$skus[$detail->sku_id];
                                    $product_name = $product->name . ($sku ? " - [" . $sku->color?->name . "] - [" . $sku->size?->name . "]" : '');
                                @endphp
                                <x-text-input id="product_name" name="product_name[]" value="{{ $product_name }}" class="mt-1 block w-full" placeholder="{{ __('Product Name') }}" readonly />
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <x-text-input id="unit_price" name="unit_price[]" value="₹{{ number_format($detail->unit_price) }}" type="text" class="mt-1 block w-full" placeholder="{{ __('Unit Price') }}" readonly />
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <x-text-input id="qty" name="qty[]" value="{{ number_format($detail->qty) }}" type="text" class="mt-1 block w-full" placeholder="{{ __('QTY') }}" readonly />
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <x-text-input id="price" name="price[]" value="₹{{ number_format($detail->price) }}" type="text" class="mt-1 block w-full" placeholder="{{ __('Price') }}" readonly />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}

    </body>
</html>
