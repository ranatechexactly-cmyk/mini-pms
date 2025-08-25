<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isManager()) {
            $projects = Project::with('manager')->latest()->paginate(5);
        } else {
            $projects = $user->projects()->with('manager')->latest()->paginate(5);
        }
        
        return view('projects.index', compact('projects'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $this->authorize('canView', $project);
        $project->load('manager', 'developers');
        return view('projects.show', compact('project'));
    }
}
