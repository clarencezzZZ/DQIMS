<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force HTTPS when behind Cloudflare tunnel
        $appUrl = config('app.url');
        
        if (str_contains($appUrl, 'trycloudflare.com')) {
            // Cloudflare tunnel - force HTTPS
            URL::forceScheme('https');
        } elseif ($this->app->environment('local') && request()->secure()) {
            // Localhost with HTTPS (Cloudflare) - respect the request scheme
            URL::forceScheme('https');
        }
    }
}
