@extends('layouts.dashboard')

@section('title', $project->name . ' - PMS')

@section('header', $project->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Project Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Description</h6>
                    <p>{{ $project->description ?? 'No description provided.' }}</p>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Manager</h6>
                        <p>{{ $project->manager->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Created At</h6>
                        <p>{{ $project->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Team Members</h5>
            </div>
            <div class="card-body">
                @if($project->developers->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($project->developers as $developer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $developer->name }}
                                <span class="badge bg-secondary">{{ ucfirst($developer->role) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-info">
                        No team members assigned to this project.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('projects.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Projects
    </a>
</div>
@endsection
