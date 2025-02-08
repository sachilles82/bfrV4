<?php

namespace App\Http\Resources\Address;

use App\Models\Address\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Address */
class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'city_id' => $this->city_id,
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
            'addressable_id' => $this->addressable_id,
            'addressable_type' => $this->addressable_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
