<?php

namespace App\Livewire\Alem\Employee\Setting\Profession\Helper;

trait ValidateStageForm
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:50' . $this->stageId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'   => __('Stage name is required.'),
            'name.min'        => __('Stage name must be at least 3 characters.'),
            'name.max'        => __('Stage name may not be greater than 50 characters.'),
        ];
    }

}
