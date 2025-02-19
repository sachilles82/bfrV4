<?php

namespace App\Livewire\Alem\Company\Helper;

use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use Illuminate\Validation\Rule;

trait ValidateCompany
{
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[a-zA-Z0-9\s]*$/'],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
            'company_size' => ['required', 'in:' . implode(',', array_column(CompanySize::cases(), 'value'))],
            'company_type' => ['required', 'in:' . implode(',', array_column(CompanyType::cases(), 'value'))],
            'email' => ['required', 'string', 'email', 'max:60', Rule::unique('companies', 'email')->ignore($this->company->id)],
            'phone_1' => ['nullable', 'string', 'regex:/^[0-9\s]+$/', 'max:20'],
            'phone_2' => ['nullable', 'string', 'regex:/^[0-9\s]+$/', 'max:20'],
            'register_number' => ['nullable', 'string', 'max:20', 'regex:/^CHE-\s\d{3}\.\d{3}\.\d{3}$/', Rule::unique('companies', 'register_number')->ignore($this->company->id)],
            'company_url' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9-]+$/', Rule::unique('companies', 'company_url')->ignore($this->company->id)],            ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => __('Please add a name.'),
            'company_name.string' => __('The entry must be a string.'),
            'company_name.min' => __('The entry must be at least 2 characters long.'),
            'company_name.regex' => __('The entry may only contain letters, numbers, and spaces.'),
            'industry_id.required' => __('Please select an industry.'),
            'industry_id.exists' => __('The selected industry is invalid.'),
            'company_size.in' => __('The selected company size is invalid.'),
            'company_type.required' => __('Please select a company type.'),
            'company_type.in' => __('The selected company type is invalid.'),
            'email.email' => __('The email must be a valid email address.'),
            'phone_1.regex' => __('Phone 1 must follow the format: +41 XX XXX XX XX.'),
            'phone_2.regex' => __('Phone 2 must follow the format: +41 XX XXX XX XX.'),
            'register_number.regex' => __('The registration number must follow the format: CHE- 123.456.789.'),
            'company_url.regex' => __('The URL may only contain letters, numbers, and dashes.'),


        ];
    }
}
