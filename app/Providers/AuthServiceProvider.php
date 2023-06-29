<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->is_superadmin ? true : null;
        });

        // Lastly check if permission or role is active on not
        Gate::after(function ($user, $ability) {
            if($user->is_superadmin != true){
                $permission = Permission::where('name', $ability)->where('status', true)->first();
                if(empty($permission)) {
                    throw new \Spatie\Permission\Exceptions\UnauthorizedException(403, "Permission is not active.");
                }
            }
            return $user->is_superadmin ? true : null;
        });
    }
}
