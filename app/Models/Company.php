<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function jobs(): HasMany
    {
        return $this->hasMany(Jobs::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
