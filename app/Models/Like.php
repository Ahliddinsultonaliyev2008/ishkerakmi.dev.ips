<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{


    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function job():BelongsTo
    {
        return $this->belongsTo(Jobs::class);
    }
    public function resume():BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

}
