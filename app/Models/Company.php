<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'logo', 'industry', 'city', 'website', 'email', 'phone', 'employees_count', 'description'])]
class Company extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_verified' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Company $company) {
            if ($company->isDirty('name') || ! $company->slug) {
                $company->slug = Str::slug($company->name).'-'.Str::lower(Str::random(5));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class);
    }
}
