<div>
    <flux:separator class="mt-2 mb-1"/>
    <flux:button
        icon="plus"
        class="w-full rounded-b-lg rounded-t-none"
        variant="filled"
        @click="$dispatch('open-modal-manager')"
    >
        {{ __('Open Manager') }}
    </flux:button>
</div>
