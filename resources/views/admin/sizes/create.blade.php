<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <a href="{{ route('admin.masters') }}">{{ __('Masters') }}</a> > <a href="{{ route('admin.sizes.index') }}">{{ __('Size') }}</a> > {{ __('Create') }}
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
                                {{ __('Create New Size') }}
                            </h2>
                        </header>

                        <form method="post" action="{{ route('admin.sizes.store') }}" class="mt-6 space-y-6">
                            @csrf

                            <div>
                                <x-input-label for="code" :value="__('Size Code')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" placeholder="{{ __('Size Code') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('code')" />
                            </div>

                            <div>
                                <x-input-label for="name" :value="__('Size Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="{{ __('Size Name') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <x-textarea id="description" name="description" class="mt-1 block w-full" :value="old('description')" placeholder="{{ __('Description') }}" >{{ old('description') }}</x-textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <div>
                                <x-input-label for="rank" :value="__('Rank')" />
                                <x-text-input id="rank" name="rank" type="number" class="mt-1 block w-full" :value="old('rank', 999)" placeholder="{{ __('Rank') }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('rank')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <input type="checkbox" id="is_active" name="is_active" class="hidden tgl-icon" checked data-target="is_active_icon" />
                                <x-input-label for="is_active" class="flex items-center cursor-pointer justify-center w-8 h-8 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:scale-105 transition-transform">
                                    <span id="is_active_icon" class="text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                </x-input-label>
                                <x-input-label>{{ __('Active') }}</x-input-label>
                            </div>

                            <div class="flex items-center gap-4">
                                <a href="{{ route('admin.sizes.index') }}">
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
                function toggleIcon(checkbox, icon) {
                    if (checkbox.checked) {
                        icon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        `;
                    } else {
                        icon.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        `;
                    }
                }

                $('body').on('change', '[name="is_active"]', function () {
                    toggleIcon(this, document.getElementById($(this).data('target')));
                });
            });
        </script>
    </x-slot>
</x-app-layout>