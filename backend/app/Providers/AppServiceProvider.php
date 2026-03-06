<?php

namespace App\Providers;

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
