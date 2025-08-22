<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Task;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
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
        Project::class => ProjectPolicy::class,
        Task::class => TaskPolicy::class,
    ];
    
    protected $namespace = 'App\Policies';

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define a gate for admin users
        Gate::define('isAdmin', function ($user) {
            return $user->isAdmin();
        });

        // Define a gate for manager users
        Gate::define('isManager', function ($user) {
            return $user->isManager();
        });

        // Define a gate for developer users
        Gate::define('isDeveloper', function ($user) {
            return $user->isDeveloper();
        });
    }
}
