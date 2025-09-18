<?php

namespace App\Providers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Providers\StudentUserProvider;
use App\Providers\AdminUserProvider;
use App\Models\User;

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
    // Register custom authentication providers
    Auth::provider('student_eloquent', function ($app, array $config) {
        return new StudentUserProvider($app['hash'], $config['model']);
    });

    Auth::provider('admin_eloquent', function ($app, array $config) {
        return new AdminUserProvider($app['hash'], $config['model']);
    });

    // Set the SQL timezone to UTC+8 for PHILIPPINE TIME
    try {
        DB::statement("SET time_zone = '+08:00'");
    } catch (\Exception $e) {
        // Optionally log or ignore in non-production
        logger()->warning('Failed to set SQL timezone: ' . $e->getMessage());
    }

    // Share user avatar with all views
    View::composer('*', function ($view) {
        // Check student guard first
        if (Auth::guard('student')->check()) {
            $user = Auth::guard('student')->user();
            $userProfile = $user->studentProfile;
            $userAvatarUrl = $userProfile && $userProfile->avatar_url 
                ? asset($userProfile->avatar_url)
                : asset('images/profile/avatar5.png'); // Default avatar
            
            $view->with('userAvatarUrl', $userAvatarUrl);
        }
        // Check admin guard for teacher/admin avatars if needed
        elseif (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            // Admin/Teachers might have different profile setup
            $userProfile = $user->teacherProfile ?? $user->adminProfile;
            $userAvatarUrl = $userProfile && $userProfile->avatar_url 
                ? asset($userProfile->avatar_url)
                : asset('images/profile/avatar5.png'); // Default avatar
            
            $view->with('userAvatarUrl', $userAvatarUrl);
        }
    });
    
    }
}
