<?php

namespace App\Http\Controllers;


use App\Services\CompanyProfileServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function __construct(protected CompanyProfileServices $companyProfileServices)
    {
    }

    function profilecompany(): JsonResponse
    {

        $company = $this->companyProfileServices->profile();
        if (is_null($company)) {
            return response()->json([
                "success" => false,
                'message' => "Company Profile error"
            ]);
        }

        return response()->json([
            "success" => true,
            'company' => $company
        ]);
    }
}
