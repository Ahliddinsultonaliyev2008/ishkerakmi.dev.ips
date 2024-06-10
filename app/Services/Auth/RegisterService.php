<?php

namespace App\Services\Auth;

use App\Models\Phone;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    protected User $user;
    protected Phone $phone;

    public function __construct(User $user, Phone $phone)
    {
        $this->user = $user;
        $this->phone = $phone;
    }

    /**
     * @param array $data
     * @return bool|User
     */
    public function registerUser(array $data): bool|User
    {
        $user = User::create([
            'firstname' => $data["firstname"],
            'password'  => Hash::make($data['password'])
        ]);
        if (!$user ->exists)
            return false;

        /** @var Phone $phone */
        $phone  = Phone::create([
            'user_id' => $user->id,
            'phone' =>$data['phone']
        ]);

        if (!$phone ->exists)
            return false;

        return $user;
    }

}
