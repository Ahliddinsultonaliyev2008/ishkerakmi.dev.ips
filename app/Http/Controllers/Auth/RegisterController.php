<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\RegisterService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct(protected RegisterService $registerService)
    {
    }

    function register(Request $request)
    {
        return response()->json(['data' => $request->get('name')]);
    }


}
