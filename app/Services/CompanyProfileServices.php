<?php

namespace App\Services;

use App\Models\Company;

class CompanyProfileServices
{
    function profile(): null|Company
    {
        return Company::where('id', session('company_id'))->first();
    }
}
