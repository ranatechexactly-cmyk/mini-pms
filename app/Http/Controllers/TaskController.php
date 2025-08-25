<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of tasks.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $tasks = Task::with(['project', 'assignedTo'])->latest()->paginate(5);
        } else if ($user->isManager()) {
            $tasks = Task::whereHas('project', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->with(['project', 'assignedTo'])->latest()->paginate(5);
        } else {
            $tasks = Task::where('assigned_to', $user->id)
                ->with(['project', 'assignedTo'])
                ->latest()
                ->paginate(5);
        }
        
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['project', 'assignedTo', 'createdBy']);
        
        return view('tasks.show', compact('task'));
    }
}
