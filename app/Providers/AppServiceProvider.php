<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
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

    // Set the SQL timezone to UTC+8 for PHILIPPINE TIME
    try {
        DB::statement("SET time_zone = '+08:00'");
    } catch (\Exception $e) {
        // Optionally log or ignore in non-production
        logger()->warning('Failed to set SQL timezone: ' . $e->getMessage());
    }

    // Share user avatar with all views
    View::composer('*', function ($view) {
        if (Auth::check()) {
            $user = Auth::user();
            $userProfile = $user->studentProfile;
            $userAvatarUrl = $userProfile && $userProfile->avatar_url 
                ? asset($userProfile->avatar_url)
                : asset('images/profile/avatar5.png'); // Default avatar
            
            $view->with('userAvatarUrl', $userAvatarUrl);
        }
    });
    
    }
}
