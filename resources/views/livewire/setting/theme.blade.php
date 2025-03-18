<x-pupi.layout.form>

    <x-slot name="title">
        {{ __('Theme Settings') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Here are stored the company information')}}
    </x-slot>

    <x-slot name="form">
        <div class="max-w-3xl mx-auto px-4 py-8">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                Choose your theme
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Select between our default Indigo or Orange appearance.
            </p>

            <div class="mt-4 flex gap-4 flex-wrap">
                <!-- Indigo -->
                <label class="relative block w-32 border border-gray-200 dark:border-gray-700
                      rounded-lg cursor-pointer overflow-hidden focus:outline-hidden
                      hover:shadow-xs transition peer-checked:ring-2
                      peer-checked:ring-blue-500 dark:bg-gray-800">
                    <input
                        type="radio"
                        wire:model.live="theme"
                        value="default"
                        class="sr-only peer"
                        x-on:change="applyTheme($el.value)"
                    />
                    <div class="h-36 w-full bg-gray-100 dark:bg-gray-700">
                        <img
                            src="https://preline.co/assets/svg/pro/account-light-image.svg"
                            alt="Default (Indigo)"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="py-2 text-center">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Indigo
                </span>
                    </div>
                    <div class="absolute inset-0 hidden peer-checked:block
                        border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                </label>

                <!-- Orange -->
                <label class="relative block w-32 border border-gray-200 dark:border-gray-700
                      rounded-lg cursor-pointer overflow-hidden focus:outline-hidden
                      hover:shadow-xs transition peer-checked:ring-2
                      peer-checked:ring-blue-500 dark:bg-gray-800">
                    <input
                        type="radio"
                        wire:model.live="theme"
                        value="orange"
                        class="sr-only peer"
                        x-on:change="applyTheme($el.value)"
                    />
                    <div class="h-36 w-full bg-gray-100 dark:bg-gray-700">
                        <img
                            src="https://preline.co/assets/svg/pro/account-dark-image.svg"
                            alt="Orange"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="py-2 text-center">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Orange
                </span>
                    </div>
                    <div class="absolute inset-0 hidden peer-checked:block
                        border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                </label>

                <!-- Green -->
                <label class="relative block w-32 border border-gray-200 dark:border-gray-700
                      rounded-lg cursor-pointer overflow-hidden focus:outline-hidden
                      hover:shadow-xs transition peer-checked:ring-2
                      peer-checked:ring-blue-500 dark:bg-gray-800">
                    <input
                        type="radio"
                        wire:model.live="theme"
                        value="green"
                        class="sr-only peer"
                        x-on:change="applyTheme($el.value)"
                    />
                    <div class="h-36 w-full bg-gray-100 dark:bg-gray-700">
                        <img
                            src="https://preline.co/assets/svg/pro/account-dark-image.svg"
                            alt="Orange"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="py-2 text-center">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Green
                </span>
                    </div>
                    <div class="absolute inset-0 hidden peer-checked:block
                        border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                </label>

                <!-- Blue -->
                <label class="relative block w-32 border border-gray-200 dark:border-gray-700
                      rounded-lg cursor-pointer overflow-hidden focus:outline-hidden
                      hover:shadow-xs transition peer-checked:ring-2
                      peer-checked:ring-blue-500 dark:bg-gray-800">
                    <input
                        type="radio"
                        wire:model.live="theme"
                        value="blue"
                        class="sr-only peer"
                        x-on:change="applyTheme($el.value)"
                    />
                    <div class="h-36 w-full bg-gray-100 dark:bg-gray-700">
                        <img
                            src="https://preline.co/assets/svg/pro/account-dark-image.svg"
                            alt="Orange"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="py-2 text-center">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Blue
                </span>
                    </div>
                    <div class="absolute inset-0 hidden peer-checked:block
                        border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                </label>

                <!-- Red -->
                <label class="relative block w-32 border border-gray-200 dark:border-gray-700
                      rounded-lg cursor-pointer overflow-hidden focus:outline-hidden
                      hover:shadow-xs transition peer-checked:ring-2
                      peer-checked:ring-blue-500 dark:bg-gray-800">
                    <input
                        type="radio"
                        wire:model.live="theme"
                        value="red"
                        class="sr-only peer"
                        x-on:change="applyTheme($el.value)"
                    />
                    <div class="h-36 w-full bg-gray-100 dark:bg-gray-700">
                        <img
                            src="https://preline.co/assets/svg/pro/account-dark-image.svg"
                            alt="Orange"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="py-2 text-center">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Red
                </span>
                    </div>
                    <div class="absolute inset-0 hidden peer-checked:block
                        border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                </label>

                <!-- Lime -->
                <label class="relative block w-32 border border-gray-200 dark:border-gray-700
                      rounded-lg cursor-pointer overflow-hidden focus:outline-hidden
                      hover:shadow-xs transition peer-checked:ring-2
                      peer-checked:ring-blue-500 dark:bg-gray-800">
                    <input
                        type="radio"
                        wire:model.live="theme"
                        value="lime"
                        class="sr-only peer"
                        x-on:change="applyTheme($el.value)"
                    />
                    <div class="h-36 w-full bg-gray-100 dark:bg-gray-700">
                        <img
                            src="https://preline.co/assets/svg/pro/account-dark-image.svg"
                            alt="Orange"
                            class="h-full w-full object-cover"
                        />
                    </div>
                    <div class="py-2 text-center">
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    Lime
                </span>
                    </div>
                    <div class="absolute inset-0 hidden peer-checked:block
                        border-2 border-blue-500 rounded-lg pointer-events-none"></div>
                </label>
            </div>
        </div>
    </x-slot>

</x-pupi.layout.form>
