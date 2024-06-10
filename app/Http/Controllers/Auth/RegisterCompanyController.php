<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterCompanyStoreRequest;
use App\Services\Auth\RegisterCompanyService;
use Illuminate\Http\JsonResponse;

class RegisterCompanyController extends Controller
{
    public function __construct(protected RegisterCompanyService $registerCompanyService)
    {
    }

    public function registercompany(RegisterCompanyStoreRequest $request): JsonResponse
    {
        $company = $this->registerCompanyService->registerCompany($request->post());
        if (is_bool($company))
            return response()->json([
                'success' => false,
                'message' => "Company Register error",
            ]);


        return response()->json([
            'success' => true,
            'message' => "Company Register success",
            'password' => $company->password,
            'login' => $company->admin,
        ], 201);
    }
}
