<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create($request->safe()->except('company_name'));

            if ($user->isEmployer()) {
                $user->company()->create(['name' => $request->validated('company_name'), 'phone' => $user->phone]);
            }

            return $user;
        });

        return $this->tokenResponse($user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->validated('phone'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages(['phone' => "Telefon raqam yoki parol noto'g'ri."]);
        }

        return $this->tokenResponse($user);
    }

    public function me(Request $request): UserResource
    {
        return UserResource::make($request->user()->load('company'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Tizimdan chiqdingiz.']);
    }

    public function updateProfile(ProfileRequest $request): UserResource
    {
        $data = $request->safe()->except(['current_password', 'password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $request->user()->update($data);

        return UserResource::make($request->user()->load('company'));
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $user->createToken('web')->plainTextToken,
            'user' => UserResource::make($user->load('company')),
        ], $status);
    }
}
