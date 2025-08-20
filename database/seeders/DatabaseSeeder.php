<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create manager user
        $manager = User::create([
            'name' => 'Project Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        // Create developer users
        $developer1 = User::create([
            'name' => 'Developer One',
            'email' => 'developer1@example.com',
            'password' => Hash::make('password'),
            'role' => 'developer',
        ]);

        $developer2 = User::create([
            'name' => 'Developer Two',
            'email' => 'developer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'developer',
        ]);

        // Create projects
        $project1 = Project::create([
            'name' => 'E-commerce Website',
            'description' => 'Build a new e-commerce platform',
            'manager_id' => $manager->id,
        ]);

        $project2 = Project::create([
            'name' => 'Mobile App Development',
            'description' => 'Develop a cross-platform mobile application',
            'manager_id' => $manager->id,
        ]);

        // Assign developers to projects
        $project1->developers()->attach([$developer1->id, $developer2->id]);
        $project2->developers()->attach([$developer1->id]);

        // Create tasks for project 1
        Task::create([
            'title' => 'Design database schema',
            'description' => 'Create the database schema for the e-commerce platform',
            'priority' => 'high',
            'status' => 'completed',
            'deadline' => now()->addDays(7),
            'project_id' => $project1->id,
            'assigned_to' => $developer1->id,
            'created_by' => $manager->id,
        ]);

        Task::create([
            'title' => 'Implement user authentication',
            'description' => 'Set up user registration and login functionality',
            'priority' => 'high',
            'status' => 'in_progress',
            'deadline' => now()->addDays(14),
            'project_id' => $project1->id,
            'assigned_to' => $developer2->id,
            'created_by' => $manager->id,
        ]);

        // Create tasks for project 2
        Task::create([
            'title' => 'Design app wireframes',
            'description' => 'Create wireframes for the mobile app',
            'priority' => 'medium',
            'status' => 'pending',
            'deadline' => now()->addDays(5),
            'project_id' => $project2->id,
            'assigned_to' => $developer1->id,
            'created_by' => $manager->id,
        ]);
    }
}
