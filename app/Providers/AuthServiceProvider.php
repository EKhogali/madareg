<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Subscriber;
use App\Models\FollowUpPeriod;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('manage-subscriber', function ($user, Subscriber $subscriber) {
            return $user->isStaff() || $subscriber->user_id === $user->id;
        });

        Gate::define('manage-followup-period', function ($user, FollowUpPeriod $period) {
            return $user->isStaff() || $period->user_id === $user->id;
        });
    }

    protected $policies = [
        Subscriber::class => \App\Policies\SubscriberPolicy::class,
        FollowUpPeriod::class => \App\Policies\FollowUpPeriodPolicy::class,
    ];

}
