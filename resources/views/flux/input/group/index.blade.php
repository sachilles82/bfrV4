@php
$classes = Flux::classes()
    ->add('w-full flex')
    ->add('[&>[data-flux-input]]:grow')
    ->add([
        '[&>*:first-child]:rounded-r-none [&>*:last-child]:rounded-l-none',
        '[&>*:first-child>input]:rounded-r-none [&>*:last-child>input]:rounded-l-none',
        '[&>*:first-child>input]:border-r-0 [&>*:last-child>input]:border-l-0',
        '[&>*:first-child>button]:rounded-r-none [&>*:last-child>button]:rounded-l-none',
    ])
    ;
@endphp

<div {{ $attributes->class($classes) }} data-flux-input-group>
    {{ $slot }}
</div>
