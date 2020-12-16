<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();


        Gate::define('isGod', function($group_user) {
            $currentUser=auth()->user();
            return $group_user->roleName == 'god';
        });
        Gate::define('isAdmin', function($group_user) {
            return $group_user->roleName == 'admin';
        });
        Gate::define('isUser', function($group_user) {
            return $group_user->roleName == 'user';
        });
        Gate::define('isUnverified', function($group_user) {
            return $group_user->roleName == 'unverified';
        });

        //
    }
}
