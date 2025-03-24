{{-- This section should go in your create.blade.php file --}}
<div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
    <!-- Status and notifications section -->
    <div class="col-span-full"
         x-data="{
             updateStatus() {
                 if ($wire.model_status !== 'active') {
                     $wire.$set('notifications', false);
                 }
             }
         }">
        <!-- Model Status Select -->
        <div class="sm:col-span-3 mb-6">
            <x-pupi.input.group
                label="{{ __('Account Status') }}"
                for="model_status"
                badge="{{ __('Required') }}"
                model="model_status"
                help-text="{{ __('Default is Active. Non-active accounts cannot receive notifications.') }}"
                :error="$errors->first('model_status')">
                <flux:select
                    wire:model.defer="model_status"
                    x-on:change="updateStatus()"
                    id="model_status"
                    name="model_status"
                    variant="listbox"
                    placeholder="{{ __('Account Status') }}">
                    @foreach($this->modelStatusOptions as $status)
                        <flux:option value="{{ $status['value'] }}">
                            <div class="flex items-center">
                                <span class="mr-2">
                                    <x-dynamic-component
                                        :component="$status['icon'] ?? 'heroicon-o-question-mark-circle'"
                                        class="h-4 w-4 {{ $status['colors'] ?? '' }}"/>
                                </span>
                                <span>{{ $status['label'] }}</span>
                            </div>
                        </flux:option>
                    @endforeach
                </flux:select>
            </x-pupi.input.group>
        </div>

        <div class="col-span-full divide-y divide-gray-200 dark:divide-white/10 pt-0">
            <div class="px-0 sm:px-0">
                <ul role="list" class="mt-2 divide-y divide-gray-200 dark:divide-white/10">
                    <!-- Send Email Invitation Toggle -->
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium leading-6 dark:text-white text-gray-900"
                               id="invitation-label">{{ __('Send Email Invitation') }}</p>
                            <p class="text-sm dark:text-gray-400 text-gray-500"
                               id="invitation-description">
                                {{ __('Toggle to send the user an invitation email. Disabled for non-active accounts.') }}
                            </p>
                        </div>
                        <x-pupi.input.toggle.invitation
                            :model-status="$model_status"
                            :notifications="$notifications" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
