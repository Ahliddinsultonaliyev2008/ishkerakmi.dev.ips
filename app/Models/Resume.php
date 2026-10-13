<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Models\Concerns\HasEngagement;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'category_id', 'title', 'full_name', 'phone', 'email', 'city', 'birth_date', 'gender',
    'employment_type', 'experience', 'salary_expected', 'currency', 'skills', 'about', 'is_public',
])]
class Resume extends Model
{
    use HasEngagement, HasFactory;

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'experience' => Experience::class,
            'currency' => Currency::class,
            'skills' => 'array',
            'birth_date' => 'date',
            'is_public' => 'boolean',
            'salary_expected' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, fn (Builder $q, string $term) => $q->where(
                fn (Builder $q) => $q->where('title', 'like', "%{$term}%")->orWhere('about', 'like', "%{$term}%")
            ))
            ->when($filters['city'] ?? null, fn (Builder $q, string $city) => $q->where('city', $city))
            ->when($filters['category'] ?? null, fn (Builder $q, string $slug) => $q->whereHas('category', fn (Builder $q) => $q->where('slug', $slug)))
            ->when($filters['employment_type'] ?? null, fn (Builder $q, string $type) => $q->where('employment_type', $type))
            ->when($filters['experience'] ?? null, fn (Builder $q, string $exp) => $q->where('experience', $exp))
            ->latest();
    }
}
