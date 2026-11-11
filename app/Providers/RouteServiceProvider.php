<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // Most app calls are unauthenticated, so the old 60/min keyed by IP was
        // shared by every customer behind the same mobile-carrier IP (CGNAT) —
        // a few customers opening the app together exhausted it and everyone got
        // 429s, which the app shows as empty screens ("No data available").
        // Key by customer instead (token, else the user_id the app sends), and
        // keep a generous per-IP ceiling for anonymous traffic.
        RateLimiter::for('api', function (Request $request) {
            $userId = $request->user()?->id ?: $request->input('user_id');

            return $userId
                ? Limit::perMinute(300)->by('user:' . $userId)
                : Limit::perMinute(600)->by('ip:' . $request->ip());
        });

        // Routes that send an SMS / email cost money and can be abused, so they
        // keep a tight limit per destination and per IP.
        RateLimiter::for('otp', function (Request $request) {
            $target = $request->input('mobile') ?: $request->input('email') ?: 'none';

            return [
                Limit::perMinute(5)->by('otp-target:' . $target),
                Limit::perMinute(20)->by('otp-ip:' . $request->ip()),
            ];
        });
    }
}
