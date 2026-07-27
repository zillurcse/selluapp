<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\Automation\OrderPlaced::class,
            [\App\Listeners\Automation\SendToN8n::class, 'handle']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\Automation\ReviewSubmitted::class,
            [\App\Listeners\Automation\SendToN8n::class, 'handle']
        );
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\Automation\MessageReceived::class,
            [\App\Listeners\Automation\SendToN8n::class, 'handle']
        );
    }
}
