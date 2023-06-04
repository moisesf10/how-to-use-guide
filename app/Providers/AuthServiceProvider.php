<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
 use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        Gate::define('allows_access_topic', [\App\Policies\WorkspacePolicy::class, 'allowsAccessTopic']);
        Gate::define('allows_access_landingpage', [\App\Policies\WorkspacePolicy::class, 'allowsAccessLandingPage']);
        Gate::define('allows_access_block', [\App\Policies\WorkspacePolicy::class, 'allowsAccessBlock']);
    }

    /*
     * Override Method
     *
     */
    public function register()
    {
        $this->app->singleton(GateContract::class, function ($app) {
            return new \Illuminate\Auth\Access\Gate($app, function () use($app) {
                // $user = call_user_func($app['auth']->userResolver());
                $user = auth()->user() ?? auth()->guard('admin')->user();

                return $user;
            });
        });
    }


}
