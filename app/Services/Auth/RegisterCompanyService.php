<?php

namespace App\Services\Auth;

use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class RegisterCompanyService
{
    protected Company $company;


    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @param array $data
     * @return bool|Company
     */
    public function registerCompany(array $data): bool|Company
    {

        $company = Company::create([
            'name' => $data['name'],
            'image' => $data['image'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profession' => $data['profession'],
            'admin' => $data['admin'],
        ]);

        if (!$company->exists) {
            return false;
        }

        return $company;
    }


}
