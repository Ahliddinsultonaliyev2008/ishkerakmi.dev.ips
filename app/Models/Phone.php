<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 * @property mixed $user_id
 */
class Phone extends Model
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
}
