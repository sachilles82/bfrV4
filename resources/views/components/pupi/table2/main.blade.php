<div class="px-6 sm:px-6 lg:px-8">

    <div class="my-6 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="relative">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead>

                    {{ $head }}

                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">

                    {{ $body }}

                    </tbody>

                    <!-- Ladeoverlay: reagiert auf alle Livewire-Anfragen -->
                    <div wire:loading class="absolute inset-0 dark:bg-gray-900 bg-gray-50 opacity-50"></div>
                    <div wire:loading.flex class="flex justify-center items-center absolute inset-0">
                        <x-pupi.icon.spinner class="text-gray-500 dark:text-indigo-600"/>
                    </div>

                </table>
            </div>
        </div>
    </div>
</div>
