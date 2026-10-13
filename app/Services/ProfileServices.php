<?php

namespace App\Services;

use App\Models\User;

class ProfileServices
{
    function profile(): null|User
    {
        return User::where('id', auth('sanctum')->id())->first();
    }
}
