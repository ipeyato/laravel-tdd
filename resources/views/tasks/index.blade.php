@extends('layouts.app')

@section('content')
<h1 class="page-header text-center">Tasks Management</h1>
<div class="row justify-content-center pt-3">
    <div class="col-md-4">
        <h2>Tasks</h2>
        <ul class="list-group">
            @foreach ($tasks as $task)
            <li class="list-group-item {{ $task->is_done ? 'task-done' : ''}}">
                <form action="{{ url('tasks/'.$task->id) }}" method="post" class="float-right"
                    onsubmit="return confirm('Are U sure to delete this task?')">
                    {{ csrf_field() }}
                    {{ method_field('delete') }}
                    <input type="submit" value="X" id="delete_task_{{ $task->id }}" class="btn btn-link btn-sm">
                    <a href="{{ url('tasks') }}?action=edit&id={{ $task->id }}" id="edit_task_{{ $task->id }}">
                        edit
                    </a>
                </form>
                <form action="{{ url('/tasks/'.$task->id.'/toggle') }}" method="post">
                    {{ csrf_field() }}
                    {{ method_field('patch') }}
                    <input type="submit" value="{{ $task->name }}" id="toggle_task_{{ $task->id }}"
                        class="btn btn-link no-padding">
                </form>
                {{ $task->name }} <br>
                {{ $task->description }}
            </li>
            @endforeach
        </ul>
    </div>
    <div class="col-md-4">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul class="list-unstyled">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if (! is_null($editTableTask) && request('action') == 'edit')
        <h2>Edit Task {{ $editTableTask->name }}</h2>
        <form id="edit_task_{{ $editTableTask->id }}" action="{{ url('tasks/'.$editTableTask->id) }}" method="post">
            {{ csrf_field() }}
            {{ method_field('patch') }}
            <div class="form-group">
                <label for="name" class="control-label">Name</label>
                <input id="name" name="name" class="form-control" type="text"
                    value="{{ old('name', $editTableTask->name) }}">
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Description</label>
                <textarea id="description" name="description"
                    class="form-control">{{ old('description', $editTableTask->description) }}</textarea></div>
            <input type="submit" value="Update Task" class="btn btn-primary">
            <a href="{{ url('tasks') }}" class="btn btn-default">Cancel</a>
        </form>
        @else
        <h2>New Task</h2>
        <form action="{{ url('tasks') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name" class="control-label">Name</label>
                <input id="name" name="name" class="form-control" type="text" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label for="description" class="control-label">Description</label>
                <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
            </div>
            <input type="submit" value="Create Task" class="btn btn-primary">
        </form>
        @endif
    </div>

</div>
@endsection
