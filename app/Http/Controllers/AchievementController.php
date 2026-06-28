<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Project;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::with('project')->latest('achieved_date')->paginate(20);
        return view('achievements.index', compact('achievements'));
    }

    public function create()
    {
        $projects = Project::orderBy('name')->get();
        return view('achievements.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required',
            'achieved_date' => 'required|date',
        ]);
        Achievement::create($request->only(['title', 'description', 'project_id', 'achieved_date', 'type']));
        return redirect()->route('achievements.index')->with('success', 'Achievement recorded.');
    }

    public function show(Achievement $achievement)
    {
        $achievement->load('project');
        return view('achievements.show', compact('achievement'));
    }

    public function edit(Achievement $achievement)
    {
        $projects = Project::orderBy('name')->get();
        return view('achievements.edit', compact('achievement', 'projects'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate(['title' => 'required', 'achieved_date' => 'required|date']);
        $achievement->update($request->only(['title', 'description', 'project_id', 'achieved_date', 'type']));
        return redirect()->route('achievements.index')->with('success', 'Achievement updated.');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        return redirect()->route('achievements.index')->with('success', 'Achievement deleted.');
    }
}
