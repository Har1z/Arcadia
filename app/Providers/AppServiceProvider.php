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
        // SECURITY: rate limit login attempts to 5 per minute. Keyed by
        // email + IP so one attacker cannot lock out everyone else behind
        // the same IP, and one email cannot be brute-forced from many IPs
        // without per-IP pressure.
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(
                $request->string('email')->lower().'|'.$request->ip()
            );
        });
    }
}
