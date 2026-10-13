<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Enums\VacancyStatus;
use App\Models\Concerns\HasEngagement;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id', 'title', 'description', 'requirements', 'benefits', 'city', 'address',
    'employment_type', 'experience', 'salary_from', 'salary_to', 'currency', 'skills', 'status',
])]
class Vacancy extends Model
{
    use HasEngagement, HasFactory;

    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'experience' => Experience::class,
            'currency' => Currency::class,
            'status' => VacancyStatus::class,
            'skills' => 'array',
            'salary_from' => 'integer',
            'salary_to' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', VacancyStatus::Active);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['q'] ?? null, fn (Builder $q, string $term) => $q->where(
                fn (Builder $q) => $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('company', fn (Builder $q) => $q->where('name', 'like', "%{$term}%"))
            ))
            ->when($filters['city'] ?? null, fn (Builder $q, string $city) => $q->where('city', $city))
            ->when($filters['category'] ?? null, fn (Builder $q, string $slug) => $q->whereHas('category', fn (Builder $q) => $q->where('slug', $slug)))
            ->when($filters['employment_type'] ?? null, fn (Builder $q, string $type) => $q->where('employment_type', $type))
            ->when($filters['experience'] ?? null, fn (Builder $q, string $exp) => $q->where('experience', $exp))
            ->when($filters['company'] ?? null, fn (Builder $q, string $id) => $q->where('company_id', $id))
            ->when($filters['salary_min'] ?? null, fn (Builder $q, $min) => $q->where(
                fn (Builder $q) => $q->where('salary_to', '>=', $min)->orWhere('salary_from', '>=', $min)
            ))
            ->when(($filters['sort'] ?? 'latest') === 'salary', fn (Builder $q) => $q->orderByDesc('salary_to'), fn (Builder $q) => $q->latest());
    }
}
