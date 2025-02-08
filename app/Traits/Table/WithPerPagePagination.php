<?php

namespace App\Traits\Table;

use Livewire\WithPagination;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait WithPerPagePagination
{

    use WithPagination;

    public int $perPage = 5;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */

    public function initializeWithPerPagePagination(): void
    {
        $this->perPage = session()->get('perPage', $this->perPage);
    }

    public function updatedPerPage(int $value): void
    {
        session()->put('perPage', $value);
    }

    public function applyPagination($query)
    {
        return $query->simplePaginate($this->perPage);
    }

    public function applySimplePagination($query)
    {
        return $query->simplePaginate($this->perPage);
    }
}



