@props([
    'variant' => 'block',
])

@php
    $classes = Flux::classes()
        ->add('[&:has([data-flux-control][disabled])_[data-flux-label]]:opacity-50') // Dim labels when the field is disabled...
        ->add(match ($variant) {
            default => 'block',
            'inline' => [
                'grid gap-x-3 gap-y-1.5',
                'has-[[data-flux-label]~[data-flux-control]]:grid-cols-[1fr_auto]',
                'has-[[data-flux-control]~[data-flux-label]]:grid-cols-[auto_1fr]',
                '[&>[data-flux-control]~[data-flux-description]]:row-start-2 [&>[data-flux-control]~[data-flux-description]]:col-start-2',
                '[&>[data-flux-control]~[data-flux-error]]:col-span-2 [&>[data-flux-control]~[data-flux-error]]:mt-1', // Position error messages...
                '[&>[data-flux-label]~[data-flux-control]]:row-start-1 [&>[data-flux-label]~[data-flux-control]]:col-start-2',
            ],
        })
        ->add(match ($variant) {
            default => [ // Adjust spacing around label...
                '[&>[data-flux-label]]:mb-1 [&>[data-flux-label]:has(+[data-flux-description])]:mb-1',
            ],
            'inline' => '',
        })
        ->add(match ($variant) {
            default => [ // Adjust spacing around description...
                '[&>[data-flux-label]+[data-flux-description]]:mt-0',
                '[&>[data-flux-label]+[data-flux-description]]:mb-1',
                '[&>*:not([data-flux-label])+[data-flux-description]]:mt-3',
            ],
            'inline' => '',
        });
@endphp

<ui-field {{ $attributes->class($classes) }} data-flux-field>
    {{ $slot }}
</ui-field>
