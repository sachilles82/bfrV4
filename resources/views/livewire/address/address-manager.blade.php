<div>

    <livewire:address.city-form
        :countries="$countries"
        lazy
    />

    <livewire:address.state-form
        :countries="$countries"
        lazy
    />

    <livewire:address.address-form
        :addressable="$addressable"
        :countries="$countries"
{{--        lazy--}}
    />

</div>
