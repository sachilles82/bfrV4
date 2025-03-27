@props(['colspan' => 5])

<tr>
    <td colspan="{{ $colspan }}" class="bg-yellow-50 px-4 py-2 text-yellow-800">
        <div class="flex items-start">
            <x-pupi.icon.danger class="h-6 w-6"/>
            <div class="ml-3 flex-1 pt-0.5">
                <p class="text-sm font-medium">{{ __('Attention!') }}
                    <span class="ml-2 font-normal text-sm text-gray-600">{{ __('Trash will delete automatically all') }}</span>
                    <span class="font-medium">{{ __('7 Days') }}</span>
                    <span class="font-normal text-sm text-gray-600">{{ __('automatically permanently') }}</span>
                </p>
            </div>
        </div>
    </td>
</tr>
