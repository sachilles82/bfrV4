<?php

namespace App\Traits\Table;

use Livewire\Attributes\Url;

trait WithSorting
{
    #[Url]
    public $sortCol;

    #[Url]
    public $sortAsc = false;

    public function sortBy($column): void
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }

}



