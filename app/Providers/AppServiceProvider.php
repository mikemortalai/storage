<?php

namespace App\Providers;

use App\Services\FacilityContext;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FacilityContext::class);
    }

    public function boot(): void
    {
        //
    }
}
