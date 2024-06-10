<?php

namespace App\Services\Auth;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    protected User $user;
    protected Phone $phone;

    public function __construct(User $user, Phone $phone)
    {
        $this->user = $user;
        $this->phone = $phone;
    }


    public function loginUser(array $data): JsonResponse
    {
        /** @var Phone $phone */
        $phone = Phone::where('phone', $data['phone'])->first();

        if (is_null($phone))
            return response()->json([
                'success' => false,
                'message' => "Phone not fount",
            ], 404);


        /** @var User $user */
        $user = User::where('id', $phone->user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'message' => "User not fount",
            ], 404);


        if (!Hash::check($data['password'], $user-> password))
            return response()->json([
                'success' => false,
                'message' => "Password invalid",
            ], 401);

        $token = $user->createToken('Auth-Token-' . $user->id)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => "User login success",
            'token' => $token,
            'user_id' => $user->id,
        ], 201);

    }

}
