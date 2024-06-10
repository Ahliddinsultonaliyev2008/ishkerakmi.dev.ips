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
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $company = Company::where('admin', $_SERVER['PHP_AUTH_USER'])->first();

        if (is_null($company))
            return response()->json(['error' => true]);

        if (!Hash::check($_SERVER['PHP_AUTH_PW'], $company->password))
            return response()->json(['error_pass' => true]);


        session(['company_id' => $company->id]);
        session(['user_id' => $company->user_id]);
        return $next($request);
    }
}
