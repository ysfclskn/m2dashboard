<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRole;
use App\Enums\ProjectStatus;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $projects = Project::where('owner_id', $user->id)
            ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id))
            ->withCount(['tasks', 'members'])
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $statuses = ProjectStatus::cases();
        return view('projects.create', compact('statuses'));
    }

    public function store(StoreProjectRequest $request)
    {
        $slug = Str::slug($request->name);
        $original = $slug;
        $i = 1;
        while (Project::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        $project = Project::create([
            ...$request->validated(),
            'owner_id' => auth()->id(),
            'slug'     => $slug,
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id'    => auth()->id(),
            'role'       => ProjectRole::Owner,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Game project created!');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['owner', 'members.user']);

        $activeSprint    = $project->activeSprint();
        $taskStats       = $project->tasks()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
        $recentTasks     = $project->tasks()->with('assignee')->latest()->take(5)->get();
        $latestReport    = $project->progressReports()->first();
        $latestWikiPages = $project->wikiPages()->latest()->take(3)->get();

        return view('projects.show', compact(
            'project', 'activeSprint', 'taskStats', 'recentTasks', 'latestReport', 'latestWikiPages'
        ));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $statuses = ProjectStatus::cases();
        return view('projects.edit', compact('project', 'statuses'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted.');
    }
}
