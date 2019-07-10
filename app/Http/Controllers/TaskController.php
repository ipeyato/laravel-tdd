<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        $editTableTask = null;

        if (request('id') && request('action') == 'edit') {
            $editTableTask = Task::find(request('id'));
        }

        return view('tasks.index', compact('tasks', 'editTableTask'));
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

        Task::create(request()->only('name', 'description'));

        return back();
    }

    public function update(Task $task)
    {
        $taskData = request()->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:255'
        ]);

        $task->update($taskData);

        return redirect('/tasks');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect('/tasks');
    }

    public function toggle(Task $task)
    {
        if ($task->is_done == 1) {
            $task->is_done = 0;
        } else {
            $task->is_done = 1;
        }
        
        $task->save();

        return redirect('/tasks');
    }
}
