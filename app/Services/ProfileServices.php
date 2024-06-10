<?php

namespace App\Services;

use App\Models\User;

class ProfileServices
{
    function profile(): null|User
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if ($user->exists())
            return $user;
        else
            return null;
    }
}
