<?php

namespace App\Providers;

use App\Models\App;
use Illuminate\Support\Facades\Route;
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
        // Resolve app only if owned by the current user (404 for others' apps)
        Route::bind('app', function (string $value) {
            $user = auth()->user();
            if (! $user) {
                return App::findOrFail($value);
            }
            return App::ownedBy($user->id)->findOrFail($value);
        });
    }
}
