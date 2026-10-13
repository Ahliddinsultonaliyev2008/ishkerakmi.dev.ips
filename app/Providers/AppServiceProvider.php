<?php

namespace App\Providers;

use App\Models\Resume;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'vacancy' => Vacancy::class,
            'resume' => Resume::class,
            'user' => User::class,
        ]);

        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());
    }
}
