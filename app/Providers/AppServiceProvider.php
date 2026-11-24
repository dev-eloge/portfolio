<?php

namespace App\Providers;

use Anthropic\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Client Claude : la clé vient de .env (config/services.php), jamais du code
        $this->app->singleton(Client::class, fn () => new Client(
            apiKey: config('services.anthropic.key'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
