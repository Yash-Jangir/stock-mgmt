<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <a href="{{ route('admin.products.index') }}">{{ __('Product') }}</a> > {{ __('Edit') }}
        </h2>
    </x-slot>

    <x-slot name="style">
        <link rel="stylesheet" href="{{ asset('/assets/css/dropzone.min.css') }}">
        <style>
            #first-image-preview img {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                height: 100%;
                width: 100%;
                object-fit: contain;
            }
            .dz-clickable {
                cursor: pointer;
            }
            .dz-clickable::before, #first-image-preview::before {
                content: "";
                height: 80px;
                width: 90px;
                background: url("/assets/images/imgpulesh.svg");
                background-position: center center;
                position: absolute;
                right: 0%;
                bottom: 50%;
                top: 0%;
                left: 0%;
                margin: auto;
                background-repeat: no-repeat;
                transform: scale(0.7);
                bottom: 0px;
            }
            .dz-clickable div:not(.dz-preview, .dz-image), .dz-clickable .dz-remove {
                display: none !important;
            }
            .dz-clickable .dz-preview {
                border: 1px solid #3e3e3e;
                border-radius: 7px;
            }
            .dz-clickable .dz-preview, .dz-clickable .dz-preview .dz-image {
                position: relative;
                height: 100%;
                width: 100%;
            }
            .dz-clickable .dz-preview .dz-image img {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            .dz-clickable .dz-preview:hover .dz-remove {
                font-size: 0;
                display: block !important;
                position: absolute;
                bottom: 2px;
                right: 2px;
            }
            .dz-clickable .dz-remove::before {
                content: "";
                background-image: url("/assets/images/greyrund.svg");
                height: 30px;
                width: 30px;
                background-position: center center;
                position: absolute;
                right: 1px;
                bottom: 2px;
                background-repeat: no-repeat;
                transform: scale(1.3);
            }
            .dz-clickable .dz-remove::after {
                content: "";
                font-size: 0px;
                background-image: url("/assets/images/redremove.svg");
                height: 26px;
                width: 23px;
                background-position: center center;
                position: absolute;
                right: 4px;
                bottom: 9px;
                background-repeat: no-repeat;
                transform: scale(1.3);
            }
        </style>
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


    <section class="py-8 gap-2">
        <form id="update-form" method="post" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div id="dropzone-popup" class="hidden fixed w-full h-full z-10 top-0 left-0 flex items-center justify-center  bg-white dark:bg-gray-800/[.25]">
                <div class="flex flex-col bg-white dark:bg-gray-800 p-4 rounded-lg w-2/3 h-1/4 shadow border border-gray-700">
                    <div class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight underline my-2">{{ __('Upload Image') }}</div>
                    <div id="dropzone-elm" class="w-full h-full cursor-pointer grid grid-cols-5 gap-4">

                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-2">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <header>
                        <h2 class="mb-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Edit Product') }}
                        </h2>
                    </header>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="grid grid-cols-2 gap-4 col-span-2">
                            <div>
                                <x-input-label for="code" :value="__('Product Code')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $product->code)" placeholder="{{ __('Product Code') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('code')" />
                            </div>

                            <div>
                                <x-input-label for="name" :value="__('Product Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product->name)" placeholder="{{ __('Product Name') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div class="col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <x-textarea id="description" name="description" class="mt-1 block w-full" :value="old('description')" placeholder="{{ __('Description') }}" >{{ old('description', $product->description) }}</x-textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected($category->id == $product->category_id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>

                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select name="gender" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->value }}" @selected($gender->value == $product->gender)>{{ $gender->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                            </div>

                            <div>
                                <x-input-label for="price" :value="__('Price')" />
                                <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" :value="old('price', (int) $product->price)" placeholder="{{ __('Price') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>
                            
                            <div>
                                <x-input-label for="age_group" :value="__('Age Group')" />
                                <select name="age_group" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value=""></option>
                                    @foreach ($ageGroups as $ageGroup)
                                        <option value="{{ $ageGroup->value }}" @selected($ageGroup->value == $product->age_group)>{{ $ageGroup->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('age_group')" />
                            </div>
                        </div>

                        <!-- images -->
                        <div>
                            <div id="first-image-preview" class="relative cursor-pointer overflow-hidden w-full h-full border border-gray-700 rounded-lg flex items-center justify-center">
                                <img src="#" class="hidden">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-4">
                        <input type="checkbox" id="is_active" name="is_active" class="hidden tgl-icon" @checked($product->is_active) data-target="is_active_icon" />
                        <x-input-label for="is_active" class="flex items-center cursor-pointer justify-center w-8 h-8 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:scale-105 transition-transform">
                            <span id="is_active_icon" class="text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentcolor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </x-input-label>
                        <x-input-label>{{ __('Active') }}</x-input-label>
                    </div>
                </div>
                
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="mb-2 text-md font-medium text-gray-900 dark:text-gray-100">
                            {{ __('SKUs') }}
                        </h2>
                        <button id="add-sku" type="button" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-400 transition ease-in-out duration-150">{{ __('+ Add Sku') }}</button>
                    </div>

                    <div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('#No.') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __ ('Color') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Size') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Price') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Description') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>

                            <tbody id="sku-wrapper" class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($product->skus ?? collect() as $sku)
                                    @php $uniqueId = uniqid(); @endphp
                                    <tr data-id="{{ $uniqueId }}" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="sr-no px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <select name="color_id[]" id="color-{{ $uniqueId }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                                <option value=""></option>
                                                @foreach ($colors as $color)
                                                    <option value="{{ $color->id }}" @selected($color->id == $sku->color_id)>{{ $color->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <select name="size_id[]" id="size-{{ $uniqueId }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                                <option value=""></option>
                                                @foreach ($sizes as $size)
                                                    <option value="{{ $size->id }}" @selected($size->id == $sku->size_id)>{{ $size->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <x-text-input name="sku_price[]" type="number" class="block w-full" :value="(int) $sku->price" placeholder="{{ __('Price') }}" required />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <x-textarea name="sku_description[]" placeholder="{{ __('Description') }}">{{ $sku->description }}</x-textarea>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-text-input id="is_active_{{ $uniqueId }}" name="sku_is_active[]" type="checkbox" :checked="(bool) $sku->is_active" class="hidden" />
                                            <label for="is_active_{{ $uniqueId }}" class="cursor-pointer inline-flex px-2 text-xs leading-5 font-semibold rounded-full 
                                                @if ($sku->is_active)
                                                    bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                @else
                                                    bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                @endif
                                            ">{{ $sku->is_active ? 'Active' : 'In-active' }}</label>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <input type="hidden" name="sku_id[]" value="{{ $sku->id }}">
                                            <div class="flex items-center justify-end gap-4">
                                                <x-danger-button class="remove-sku" type="button">{{ __('Remove') }}</x-danger-button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                        
                </div>

                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.products.index') }}">
                            <x-secondary-button>{{ __('Cancel') }}</x-secondary-button>
                        </a>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <x-slot name="script">
        <script src="{{ asset('/assets/js/dropzone.min.js') }}"></script>
        <script>
            Dropzone.autoDiscover = false;

            $(document).ready(function() {
                const dropzone = new Dropzone('#dropzone-elm', {
                    url: '/',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    maxFilesize: 20,
                    maxFiles: 5,
                    acceptedFiles: 'image/*',
                    addRemoveLinks: true,
                    dictRemoveFile: 'x',
                    autoProcessQueue: false,
                    thumbnailMethod: 'contain',
                    thumbnailWidth: 300,
                    thumbnailHeight: 300,
                    init: function () {
                        @php 
                            $files = $product->images->map(fn ($mediaItem) => ([
                                            'name' => $mediaItem->file_name,
                                            'size' => $mediaItem->size,
                                            'url'  => $mediaItem->getUrl('thumb'),
                                            'id'   => $mediaItem->id
                                        ]))->toArray();
                        @endphp

                        const dropzone = this;
                        const existingMedia = @json($files);

                        existingMedia.forEach(function (file) {
                            let mockFile = {
                                name: file.name,
                                size: file.size,
                                type: 'image/*',
                                accepted: true,
                            };

                            dropzone.emit("addedfile", mockFile);
                            dropzone.emit("thumbnail", mockFile, file.url);
                            dropzone.emit("complete", mockFile);

                            dropzone.files.push(mockFile);

                            mockFile.existingId = file.id;
                        });
                    },
                    removedfile: function (file) {
                        if (confirm("Are you sure?")) {
                            file.previewElement?.remove();

                            if (file.existingId) {
                                let input   = document.createElement('input');
                                input.type  = 'hidden';
                                input.name  = 'deleted_images_ids[]';
                                input.value = file.existingId;
                                $(this.element).closest('form')[0].appendChild(input);
                            }

                            this.files = this.files.filter(f => f !== file);
                        }
                    },
                    error: function (file, resp) {
                        file.previewElement?.remove();
                        alert(resp);
                    }
                });

                function toggleIcon(checkbox, icon) {
                    if (checkbox.checked) {
                        icon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentcolor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        `;
                    } else {
                        icon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentcolor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        `;
                    }
                }

                $('body').on('change', '[name="is_active"]', function () {
                    toggleIcon(this, document.getElementById($(this).data('target')));
                });


                const resetSrNo = () => $('#sku-wrapper .sr-no').each((i, e) => $(e).text(i + 1));

                const checkIfExists = (color, size, tr) => ($('#sku-wrapper tr').not(tr).filter(function () {
                    return $(this).find('select[name="color_id[]"]').val() === color && $(this).find('select[name="size_id[]"]').val() === size;
                }).length > 0);

                $('#add-sku').click(function () {
                    const uniqueId = Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2);

                    $('#sku-wrapper').append(`
                        <tr data-id="${uniqueId}" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="sr-no px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">${ $('#sku-wrapper tr').length + 1 }</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                <select name="color_id[]" id="color-${uniqueId}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value=""></option>
                                    @foreach ($colors as $color)
                                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <select name="size_id[]" id="size-${uniqueId}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                    <option value=""></option>
                                    @foreach ($sizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <x-text-input name="sku_price[]" type="number" class="block w-full" placeholder="{{ __('Price') }}" required />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <x-textarea name="sku_description[]" placeholder="{{ __('Description') }}" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-text-input id="is_active_${uniqueId}" name="sku_is_active[]" type="checkbox" checked class="hidden" />
                                <label for="is_active_${uniqueId}" class="cursor-pointer inline-flex px-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</label>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-4">
                                    <x-danger-button class="remove-sku" type="button">{{ __('Remove') }}</x-danger-button>
                                </div>
                            </td>
                        </tr>
                    `);

                    $('#sku-wrapper tr').last().find(`[name="sku_price[]"]`).val($(`[name="price"]`).val());
                });

                $('body').on('click', '.remove-sku', function () {
                    $(this).closest('tr').remove();
                    resetSrNo();
                });

                $('body').on('change', `[name="color_id[]"], [name="size_id[]"]`, function () {
                    const tr    = $(this).closest('tr');
                    const color = tr.find('select[name="color_id[]"]').val();
                    const size  = tr.find('select[name="size_id[]"]').val();

                    if (color && size && checkIfExists(color, size, tr)) {
                        alert('This combination already exists.');
                        $(this).val('');
                    }
                });

                $('body').on('change', `[name="sku_is_active[]"]`, function () {
                    const label = $(`label[for="${$(this).attr('id')}"]`);

                    if ($(this).prop('checked')) {
                        label.removeClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300');
                        label.addClass('bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300');
                        label.text('Active');
                    } else {
                        label.removeClass('bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300');
                        label.addClass('bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300');
                        label.text('In-active');
                    }
                });

                $('#update-form').on('submit', function (e) {
                    e.preventDefault();
                    const form = this;

                    $('#sku-wrapper tr').each(function (index) {
                        const checkbox = $(this).find('[name="sku_is_active[]"]');
                        const name = $(checkbox).attr('name').replace('[]', '');
                        $(checkbox).attr('name', `${name}[${index}]`);
                    });

                    const createFileList = (file) => {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        return dataTransfer.files;
                    }

                    dropzone.getAcceptedFiles().forEach((file, index) => {
                        if (file.existingId) return;

                        const fileInput = document.createElement('input');
                        fileInput.type  = 'file';
                        fileInput.name  = 'images[]';
                        fileInput.files = createFileList(file);
                        fileInput.style.display = 'none';
                        form.appendChild(fileInput);
                    });

                    setTimeout(() => form.submit(), 100);
                });

                $('#first-image-preview').click(function () {
                    $('#dropzone-popup').removeClass('hidden');
                });
                $('#dropzone-popup').click(function () {
                    $('#dropzone-popup').addClass('hidden');
                    const img = $('#dropzone-popup img').first();
                    if (img.length) {
                        $('#first-image-preview img').attr('src', img.attr('src')).removeClass('hidden');
                    } else {
                        $('#first-image-preview img')[0].classList.toggle('hidden', true);
                    }
                });
                $('#dropzone-popup > div').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });

                setTimeout(() => { 
                    $('#dropzone-popup').click(),
                    $('.tgl-icon').each(function() {
                        toggleIcon(this, document.getElementById($(this).data('target')));
                    });
                }, 500);
            });
        </script>
    </x-slot>
</x-app-layout>