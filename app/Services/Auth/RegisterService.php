<?php

namespace App\Services\Auth;

use App\Models\User;

class RegisterService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUserFirstname()
    {
        return $this->user->firstname;
    }

    public function registerUser(): string
    {
        return "a";
    }

}
