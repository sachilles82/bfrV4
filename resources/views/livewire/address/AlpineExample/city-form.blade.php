<div>
    <!-- Country Dropdown -->
    <div>
        <label for="country_id">{{ __('Country') }}</label>
        <select id="country_id" wire:model="country_id">
            @foreach($countries as $country)
                <option value="{{ $country['id'] }}">{{ $country['name'] }}</option>
            @endforeach
        </select>
    </div>

    <!-- State Dropdown -->
    <div>
        <label for="state_id">{{ __('State') }}</label>
        <select id="state_id" wire:model="state_id">
            @if($states->isEmpty())
                <option value="">{{ __('No states available') }}</option>
            @else
                @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <!-- Weitere Eingaben, z. B. City Name -->
    <div>
        <label for="name">{{ __('City Name') }}</label>
        <input id="name" type="text" wire:model.defer="name">
    </div>

    <!-- Save-Button -->
    <div>
        <button wire:click.prevent="saveCity">{{ __('Save City') }}</button>
    </div>
</div>
