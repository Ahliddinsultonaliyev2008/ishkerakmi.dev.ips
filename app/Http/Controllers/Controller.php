<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

abstract class Controller
{
    use AuthorizesRequests;

    protected function perPage(Request $request, int $default = 12): int
    {
        return min(max($request->integer('per_page', $default), 1), 50);
    }
}
