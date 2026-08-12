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
        $this->app->bind(
            \Filament\Notifications\Auth\ResetPassword::class,
            \App\Notifications\CustomResetPasswordNotification::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ActivityDetail::observe(ActivityDetailObserver::class);
        \App\Models\SupervisorActivityDetail::observe(\App\Observers\SupervisorActivityDetailObserver::class);
    }
}
