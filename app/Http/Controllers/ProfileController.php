<?php

namespace App\Http\Controllers;

use App\Services\ProfileServices;
use Illuminate\Http\JsonResponse;


class   ProfileController extends Controller
{

    public function __construct(protected ProfileServices $profileServices)
    {
    }

    function profile(): JsonResponse
    {

        $user = $this->profileServices->profile();
        if (is_null($user)) {
            return response()->json([
                "success" => false,
                'message' => "User Profile error"
            ]);
        }

        return response()->json([
            "success" => true,
            'user' => $user

        ]);
    }

    function logout(): JsonResponse
    {
        auth('sanctum')->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => "Logout success"
        ]);
    }
}
