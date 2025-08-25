@extends('layouts.dashboard')

@section('title', 'Projects - PMS')

@section('header', 'Projects')

@section('content')
<div class="card">
    <div class="card-body">
        @if($projects->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Manager</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $index => $project)
                            <tr>
                                <td>{{ $projects->firstItem() + $loop->index }}</td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}">
                                        {{ $project->name }}
                                    </a>
                                </td>
                                <td>{{ $project->manager->name }}</td>
                                <td>{{ Str::limit($project->description, 50) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No projects found.
            </div>
        @endif
    </div>
</div>
@endsection
