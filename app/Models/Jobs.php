<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jobs extends Model
{
    use HasFactory;
    protected $guarded =['id', 'created_at', 'updated_at',];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function comment():HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
