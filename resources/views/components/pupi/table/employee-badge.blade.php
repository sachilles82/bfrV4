@props(['status'])

@php
    if (is_string($status)) {
        $status = \App\Enums\User\EmployeeAccountStatus::from($status);
    }
    $colorClasses = $status->fullColorClasses();
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {$colorClasses}"]) }}>
    {{ $status->label() }}
</span>
