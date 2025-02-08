@props([
    'name' => null,
    'message' => null,
])

@php
$message ??= $name ? $errors->first($name) : null;

$classes = Flux::classes('mt-1 text-sm text-red-600 dark:text-red-400')
    ->add($message ? '' : 'hidden');
@endphp

<div aria-live="assertive" {{ $attributes->class($classes) }} data-flux-error>
    <?php if ($message) : ?>
{{--        <flux:icon icon="exclamation-triangle" variant="mini" class="inline" />--}}

        {{ $message }}
    <?php endif; ?>
</div>
