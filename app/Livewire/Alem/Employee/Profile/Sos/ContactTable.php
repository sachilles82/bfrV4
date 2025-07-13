<?php

namespace App\Livewire\Alem\Employee\Profile\Sos;

use App\Models\Alem\SOS;
use App\Traits\Table\WithPerPagePagination;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class ContactTable extends Component
{
    use AuthorizesRequests;
    use WithPerPagePagination;


    #[Locked]
    public ?int $userId = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    public function delete($contactId): void
    {
        try {
            $contact = SOS::find($contactId);

            //Authorize the action

            if ($contact && $contact->user_id === $this->userId) {
                $contactName = $contact->name;

                $contact->delete();

                $this->resetPage();

                Flux::toast(
                    text: __('Contact :name removed successfully.', ['name' => $contactName]),
                    heading: __('Success'),
                    variant: 'success'
                );
            }
        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error deleting contact.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        $contacts = SOS::query()
            ->select([
                'id',
                'user_id',
                'name',
                'gender',
                'related',
                'phone',
                'email',
            ])
            ->where('user_id', $this->userId)
            ->latest()
            ->simplePaginate($this->perPage);

        return view('livewire.alem.employee.profile.sos.contact-table', [
            'contacts' => $contacts
        ]);
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.sos.contacts-table');
    }
}
