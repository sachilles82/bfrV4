<?php

namespace App\Enums\Company;

enum CompanyRegistrationType: string
{

    case SELF_REGISTERED = 'Registerform';
    case ADMIN_REGISTERED = 'Adminform';

}
