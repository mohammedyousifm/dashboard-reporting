<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\Category;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project.company', 'category']);
        if ($request->status)     $query->where('status', $request->status);
        if ($request->project_id) $query->where('project_id', $request->project_id);
        $tasks    = $query->latest()->paginate(25);
        $projects = Project::orderBy('name')->get();
        return view('tasks.index', compact('tasks', 'projects'));
    }

    public function create()
    {
        $projects   = Project::with('company')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('tasks.create', compact('projects', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required', 'project_id' => 'required|exists:projects,id']);
        Task::create($request->only(['title', 'description', 'project_id', 'category_id', 'status', 'priority', 'due_date', 'completed_date']));
        return redirect()->route('tasks.index')->with('success', 'Task created.');
    }

    public function show(Task $task)
    {
        $task->load(['project.company', 'category']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects   = Project::with('company')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'projects', 'categories'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate(['title' => 'required', 'project_id' => 'required|exists:projects,id']);
        $task->update($request->only(['title', 'description', 'project_id', 'category_id', 'status', 'priority', 'due_date', 'completed_date']));
        return redirect()->route('tasks.index')->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }
}
