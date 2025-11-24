<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\ActivityDetail;
use App\Observers\ActivityDetailObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ActivityDetail::observe(ActivityDetailObserver::class);
    }
}
