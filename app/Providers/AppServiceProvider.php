<?php

namespace App\Providers;

use App\Application\Account\ListenerRepository;
use App\Application\Account\PasswordHasher;
use App\Application\Admin\AdminAccountRepository;
use App\Application\Radio\StreamConfigProvider;
use App\Infrastructure\Persistence\Eloquent\EloquentAdminAccountRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentListenerRepository;
use App\Infrastructure\Radio\ConfigStreamConfigProvider;
use App\Infrastructure\Security\LaravelPasswordHasher;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
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
        $this->app->bind(ListenerRepository::class, EloquentListenerRepository::class);
        $this->app->bind(AdminAccountRepository::class, EloquentAdminAccountRepository::class);
        $this->app->bind(PasswordHasher::class, LaravelPasswordHasher::class);
        $this->app->bind(StreamConfigProvider::class, ConfigStreamConfigProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureAuthenticatedRedirects();
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($this->loginKey($request)));
        RateLimiter::for('admin-login', fn (Request $request) => Limit::perMinute(5)->by($this->loginKey($request)));
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(10)->by((string) $request->ip()));
        RateLimiter::for('password-email', fn (Request $request) => Limit::perMinute(3)->by((string) $request->ip()));
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(5)->by((string) $request->ip()));
        RateLimiter::for('profile-password', fn (Request $request) => [
            Limit::perMinute(5)->by('profile-password|user|'.$request->user('web')?->getKey()),
            Limit::perMinute(20)->by('profile-password|ip|'.$request->ip()),
        ]);
    }

    private function loginKey(Request $request): string
    {
        $email = $request->input('email');

        return (is_string($email) ? mb_strtolower($email) : '').'|'.$request->ip();
    }

    private function configureAuthenticatedRedirects(): void
    {
        RedirectIfAuthenticated::redirectUsing(
            fn (Request $request): string => $request->is('admin', 'admin/*')
                ? route('admin.dashboard')
                : route('home')
        );
    }
}
