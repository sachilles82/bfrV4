<?php

namespace App\Livewire\Forms;

use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Livewire\Form;

class ChildForm extends Form
{
    use GenderOptions;

    public $name = '';
    public $gender = '';
    public $birthdate = '';
    public $ahv_number = '';
    public $valid_until = '';

    // Nicht validierte Properties
    public ?int $userId = null;
    public ?Child $child = null;

    // ENTFERNE mount() - das gehört hier nicht hin!

    public function setChild(Child $child): void
    {
        $this->child = $child;
        $this->name = $child->name;
        $this->gender = $child->gender?->value ?? '';
        $this->birthdate = $child->birthdate?->format('Y-m-d') ?? '';
        $this->ahv_number = $child->ahv_number ?? '';
        $this->valid_until = $child->valid_until?->format('Y-m-d') ?? '';
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function save(): void
    {
        // Auto-calculate valid_until wenn birthdate gesetzt ist
        if ($this->birthdate && !$this->valid_until) {
            $birthdate = \Carbon\Carbon::parse($this->birthdate);
            $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
        }

        Child::create([
            'user_id' => $this->userId,
            'name' => $this->name,
            'gender' => $this->gender ?: null,
            'birthdate' => $this->birthdate ?: null,
            'ahv_number' => $this->ahv_number ?: null,
            'valid_until' => $this->valid_until ?: null,
        ]);

        $this->reset(['name', 'gender', 'birthdate', 'ahv_number', 'valid_until']);
    }

    public function update(): void
    {
        // Auto-calculate valid_until wenn birthdate gesetzt ist
        if ($this->birthdate && !$this->valid_until) {
            $birthdate = \Carbon\Carbon::parse($this->birthdate);
            $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
        }

        $this->child->update([
            'name' => $this->name,
            'gender' => $this->gender ?: null,
            'birthdate' => $this->birthdate ?: null,
            'ahv_number' => $this->ahv_number ?: null,
            'valid_until' => $this->valid_until ?: null,
        ]);
    }
}
