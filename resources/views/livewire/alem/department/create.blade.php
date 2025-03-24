<div>

    <flux:modal.trigger name="create-department">
        <x-pupi.button.open-manager/>
    </flux:modal.trigger>

    <flux:modal name="create-department"
                variant="flyout" class="space-y-6">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add New Department') }}</flux:heading>
                <flux:subheading>{{ __('save a new department in database') }}</flux:subheading>
            </div>

            <flux:input wire:model="name" type="text" label="Name" placeholder="Department"/>

            <div class="flex">
                <flux:spacer/>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
