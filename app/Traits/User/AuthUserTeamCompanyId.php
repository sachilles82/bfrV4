<?php

namespace App\Traits\User;

use Livewire\Attributes\Locked;

trait AuthUserTeamCompanyId
{
    /**
     * SICHERHEIT: Locked Properties können nicht von außen manipuliert werden
     * @var int|null
     *  ID des authentifizierten Benutzers. Wird von der übergeordneten View übergeben.
     */
    #[Locked]
    public ?int $authUserId = null;

    #[Locked]
    public ?int $currentTeamId = null;

    #[Locked]
    public ?int $companyId = null;

}
