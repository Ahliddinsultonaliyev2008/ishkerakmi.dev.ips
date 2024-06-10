<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    protected $guarded =['id', 'created_at', 'updated_at',];
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
