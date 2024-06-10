<?php

namespace App\Services\Auth;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginCompanyService
{
    protected Company $company;


    public function __construct(Company $company)
    {
        $this->company = $company;
    }


    public function loginCompany(array $data): JsonResponse
    {
        /** @var Company $company */
        $company = Company::where( 'admin', $data['admin'])->first();
        if (is_null($company))
            return response()->json([
                'success' => false,
                'message' => "login not fount",
            ], 404);


        if (! Hash::check($data['password'], $company->password))
            return response()->json([
                'success' => false,
                'message' => "Password invalid",
            ], 401);


        return response()->json([
            'success' => true,
            'message' => "Company login success",
            'company' => $company->id,
        ], 201);

    }

}
