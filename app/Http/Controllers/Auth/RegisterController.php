<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterStoreRequest;
use App\Models\User;
use App\Services\Auth\RegisterService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(protected RegisterService $registerService)
    {
    }

    public function register(RegisterStoreRequest $request): JsonResponse
    {
        $user = $this->registerService->registerUser($request->post());
        if (is_bool($user))
            return response()->json([
                'success' => false,
                'message' => "User Register error",
            ]);

        $token = $user->createToken('Auth-Token-' . $user->id)->plainTextToken;


        return response()->json([
            'success' => true,
            'message' => "Foydalanuvchi ro'yxatdan muvaffaqiyatli o'tdingiz",
            'token' => $token,
            'user_id' => $user->id,
        ], 201);
    }
}
