{{-- resources/views/components/pupi/input/toggle/invitation.blade.php --}}
@props(['modelStatus' => 'active', 'notifications' => true])

<div x-data="{
    isNotificationEnabled: {{ $notifications ? 'true' : 'false' }},
    isAccountActive: '{{ $modelStatus }}' === 'active',

    init() {
        // Watch Livewire property changes without triggering Livewire updates
        this.$watch('$wire.model_status', value => {
            this.isAccountActive = value === 'active';
            if (!this.isAccountActive) {
                this.isNotificationEnabled = false;
                // Update Livewire property without triggering server update
                $wire.$set('notifications', false);
            }
        });
    }
}"
     class="mt-1 relative inline-block">
    <label @click="if(isAccountActive) { $refs.toggle.click(); $refs.toggle.focus(); }" class="sr-only">{{ __('Toggle Send Invitation') }}</label>
    <span class="mr-3 text-sm">
        <span
            :class="{
                'font-semibold text-indigo-600 dark:text-indigo-400': isNotificationEnabled,
                'text-gray-400 dark:text-gray-500': !isNotificationEnabled,
                'opacity-50': !isAccountActive
            }"
            x-text="isNotificationEnabled ? '{{ __('Send Email') }}' : '{{ __('No Email') }}'">
        </span>
    </span>

    <!-- Button as Switch -->
    <button x-ref="toggle"
            @click="if(isAccountActive) {
                isNotificationEnabled = !isNotificationEnabled;
                $wire.$set('notifications', isNotificationEnabled);
            }"
            type="button"
            role="switch"
            :disabled="!isAccountActive"
            :aria-checked="isNotificationEnabled.toString()"
            :class="{
                'bg-indigo-600 dark:bg-indigo-400/10 dark:ring-indigo-400/30': isNotificationEnabled,
                'bg-gray-200 dark:ring-gray-400/20 dark:bg-gray-400/10': !isNotificationEnabled,
                'opacity-50 cursor-not-allowed': !isAccountActive
            }"
            class="peer relative w-11 h-6 pt-2 rounded-full transition-colors ease-in-out duration-200 dark:ring-1 ring-inset">
        <span
            :class="{
                'translate-x-full dark:bg-indigo-400': isNotificationEnabled,
                'translate-x-0 dark:bg-gray-700': !isNotificationEnabled,
                'opacity-50': !isAccountActive
            }"
            class="before:inline-block before:size-5 before:rounded-full before:transform before:ring-0 before:transition before:ease-in-out before:duration-200 absolute inset-0.5 bg-white w-5 h-5 rounded-full shadow transition-transform">
        </span>
    </button>
</div>
