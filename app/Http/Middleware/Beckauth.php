<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class Beckauth
{
    /**
     * Authenticate a company with HTTP Basic Auth (admin login + password).
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->getUser();
        $password = $request->getPassword();

        if (is_null($admin) || is_null($password))
            return response()->json([
                'success' => false,
                'message' => "Company not authenticated",
            ], 401);

        $company = Company::where('admin', $admin)->first();

        if (is_null($company) || !Hash::check($password, $company->password))
            return response()->json([
                'success' => false,
                'message' => "Login or password invalid",
            ], 401);

        session(['company_id' => $company->id]);
        return $next($request);
    }
}
