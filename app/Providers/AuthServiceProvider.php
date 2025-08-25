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
        User::class => DashboardPolicy::class,
    ];
    
    protected $namespace = 'App\Policies';

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define role-based gates
        Gate::define('isAdmin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('isManager', function ($user) {
            return $user->isManager();
        });

        Gate::define('isDeveloper', function ($user) {
            return $user->isDeveloper();
        });

        // Navigation gates
        Gate::define('viewProjects', function ($user) {
            return $user->isAdmin() || $user->isManager() || $user->isDeveloper();
        });

        Gate::define('viewTasks', function ($user) {
            return $user->isAdmin() || $user->isManager() || $user->isDeveloper();
        });

        Gate::define('manageUsers', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('viewReports', function ($user) {
            return $user->isAdmin() || $user->isManager();
        });
    }
}
