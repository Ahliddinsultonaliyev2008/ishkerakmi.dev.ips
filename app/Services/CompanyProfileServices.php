<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;


class CompanyProfileServices
{
    function profile(): JsonResponse
    {

        $company = Company::where('admin', $_SERVER['PHP_AUTH_USER'])->where('password', $_SERVER['PHP_AUTH_PW'])->first();

        if (is_null($company)){
            return response()->json([
                'error' => false,
                'message' => "Company Profile error"
            ]);
        }

        return response()->json([
            'success' => true,
            'company' => $company
        ]);

    }
}
