@extends('layouts.dashboard')

@section('title', 'Tasks - PMS')

@section('header', 'Tasks')

@section('content')
<div class="card">
    <div class="card-body">
        @if($tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $index => $task)
                            <tr>
                                <td>{{ $tasks->firstItem() + $loop->index }}</td>
                                <td>
                                    <a href="{{ route('tasks.show', $task) }}">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($task->project)
                                        <a href="{{ route('projects.show', $task->project) }}">
                                            {{ $task->project->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No Project</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $task->status === 'completed' ? 'success' : 
                                        ($task->status === 'in_progress' ? 'primary' : 'warning') 
                                    }}">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $task->priority === 'high' ? 'danger' : 
                                        ($task->priority === 'medium' ? 'warning' : 'info') 
                                    }}">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>{{ $task->assignedTo->name ?? 'Unassigned' }}</td>
                                <td>{{ $task->deadline ? $task->deadline->format('M d, Y') : 'No deadline' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No tasks found.
            </div>
        @endif
    </div>
</div>
@endsection
