@props(['statusFilter'])

@php
    $statuses = \App\Enums\Model\ModelStatus::cases();
@endphp
<div>
    <flux:select variant="listbox" wire:model.live="statusFilter" id="statusFilter">
        @foreach($statuses as $status)
            <flux:option value="{{ $status->value }}">{{ __($status->label()) }}</flux:option>
        @endforeach
    </flux::select>
</div>


