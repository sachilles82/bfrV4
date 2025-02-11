<?php

namespace App\Livewire\HR\Company;

use App\Livewire\HR\Company\Helper\ValidateCompany;
use App\Models\HR\Company;
use App\Models\HR\Industry;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;

class CompanyUpdate extends Component
{
    use ValidateCompany, AuthorizesRequests;

    public Company $company;

    // Lokale Properties für das Formular
    public string $ownerName;
    public string $company_name;
    public int $industry_id;
    public string $company_size;
    public string $company_type;
    public ?string $email;
    public ?string $phone_1;
    public ?string $phone_2;
    public ?string $register_number;
    public ?string $company_url;

    public function mount(Company $company): void
    {
        // Sicherstellen, dass der Owner geladen ist
        $company->loadMissing('owner:id,name');
        $this->company = $company;

        // Initialisiere die Formularfelder
        $this->ownerName       = $company->owner->name ?? 'Unknown';
        $this->company_name    = $company->company_name;
        $this->industry_id     = $company->industry_id;
        $this->company_size    = $company->company_size->value ?? '';
        $this->company_type    = $company->company_type->value ?? '';
        $this->email           = $company->email;
        $this->phone_1         = $company->phone_1;
        $this->phone_2         = $company->phone_2;
        $this->register_number = $company->register_number;
        $this->company_url     = $company->company_url;
    }

    #[Computed(persist: true)]
    public function industries()
    {
        return Cache::rememberForever('industries', function () {
            return Industry::select(['id', 'name'])->get();
        });
    }


    public function updateCompany(): void
    {
        $this->authorize('update', $this->company);
        $this->validate();

        $this->company->update([
            'company_name'    => $this->company_name,
            'industry_id'     => $this->industry_id,
            'company_size'    => $this->company_size,
            'company_type'    => $this->company_type,
            'company_url'     => $this->company_url,
            'email'           => $this->email,
            'phone_1'         => $this->phone_1,
            'phone_2'         => $this->phone_2,
            'register_number' => $this->register_number,
        ]);

        // Den Cache für die Company-Daten invalidieren
        $cacheKey = "company-details-{$this->company->id}";
        Cache::forget($cacheKey);

        Flux::toast(
            text: __('Company updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );
    }

    public function render(): View
    {
        return view('livewire.hr.company.update');
    }
}
