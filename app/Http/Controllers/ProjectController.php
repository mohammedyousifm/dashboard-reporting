<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Company;
use App\Models\Category;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['company', 'category'])->withCount('tasks');
        if ($request->company_id) $query->where('company_id', $request->company_id);
        if ($request->status) $query->where('status', $request->status);
        $projects = $query->latest()->paginate(20);
        $companies = Company::orderBy('name')->get();
        return view('projects.index', compact('projects', 'companies'));
    }

    public function create()
    {
        $companies  = Company::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('projects.create', compact('companies', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required', 'company_id' => 'required|exists:companies,id']);
        Project::create($request->only(['name', 'description', 'company_id', 'category_id', 'status', 'start_date', 'end_date']));
        return redirect()->route('projects.index')->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        $project->load(['company', 'category', 'tasks', 'achievements']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $companies  = Company::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('projects.edit', compact('project', 'companies', 'categories'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate(['name' => 'required', 'company_id' => 'required|exists:companies,id']);
        $project->update($request->only(['name', 'description', 'company_id', 'category_id', 'status', 'start_date', 'end_date']));
        return redirect()->route('projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}
