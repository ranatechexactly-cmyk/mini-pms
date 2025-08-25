@extends('layouts.dashboard')

@section('title', $task->title . ' - Task Details')

@section('header', 'Task Details: ' . $task->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $task->description ?? 'No description provided.' }}</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6>Status</h6>
                            <span class="badge bg-{{ 
                                $task->status === 'completed' ? 'success' : 
                                ($task->status === 'in_progress' ? 'primary' : 'warning') 
                            }}">
                                {{ str_replace('_', ' ', ucfirst($task->status)) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Priority</h6>
                            <span class="badge bg-{{ 
                                $task->priority === 'high' ? 'danger' : 
                                ($task->priority === 'medium' ? 'warning' : 'info') 
                            }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Deadline</h6>
                            <p>{{ $task->deadline ? $task->deadline->format('M d, Y') : 'No deadline' }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h6>Project</h6>
                            @if($task->project)
                                <a href="{{ route('projects.show', $task->project) }}">
                                    {{ $task->project->name }}
                                </a>
                            @else
                                <span class="text-muted">No project</span>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <h6>Assigned To</h6>
                            <p>{{ $task->assignedTo->name ?? 'Unassigned' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Created By</h6>
                            <p>{{ $task->createdBy->name ?? 'System' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Created At</h6>
                            <p>{{ $task->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Tasks
    </a>
</div>
@endsection
