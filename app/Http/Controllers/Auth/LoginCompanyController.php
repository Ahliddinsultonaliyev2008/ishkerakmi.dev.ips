<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginCompanyRequest;
use App\Services\Auth\LoginCompanyService;

class LoginCompanyController extends Controller
{
    protected LoginCompanyService $loginCompanyService;

    public function __construct(LoginCompanyService $loginCompanyService)
    {
        $this->loginCompanyService = $loginCompanyService;
    }

    public function logincompany(LoginCompanyRequest $request)
    {
        return $this->loginCompanyService->loginCompany($request->post());
    }
}
