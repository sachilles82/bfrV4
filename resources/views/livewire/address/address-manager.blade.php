<div>
{{--    <livewire:address.state-form--}}
{{--        :countries="$countries"--}}
{{--    />--}}

{{--    <livewire:address.city-form--}}
{{--        :countries="$countries"--}}
{{--        :states="$states"--}}
{{--    />--}}



    <livewire:address.select
        :countries="$countries"
        :states="$states"
        :cities="$cities"
        lazy
    />

{{--    <livewire:address.address-form--}}
{{--        :addressable="$addressable"--}}
{{--        :countries="$countries"--}}
{{--        :states="$states"--}}
{{--        :cities="$cities"--}}
{{--    />--}}
</div>
