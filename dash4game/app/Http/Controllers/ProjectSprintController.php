<?php

namespace App\Http\Controllers;

use App\Enums\SprintStatus;
use App\Http\Requests\StoreSprintRequest;
use App\Http\Requests\UpdateSprintRequest;
use App\Models\Project;
use App\Models\Sprint;

class ProjectSprintController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $sprints = $project->sprints()->withCount('tasks')->get();

        return view('sprints.index', compact('project', 'sprints'));
    }

    public function create(Project $project)
    {
        $this->authorize('update', $project);

        $statuses = SprintStatus::cases();
        return view('sprints.create', compact('project', 'statuses'));
    }

    public function store(StoreSprintRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        // Enforce one active sprint per project
        if ($request->status === 'active') {
            $project->sprints()->where('status', 'active')->update(['status' => 'planned']);
        }

        $project->sprints()->create($request->validated());

        return redirect()->route('projects.sprints.index', $project)
            ->with('success', 'Raid created!');
    }

    public function show(Project $project, Sprint $sprint)
    {
        $this->authorize('view', $project);
        abort_if($sprint->project_id != $project->id, 404);

        $tasks = $sprint->tasks()->with(['assignee'])->orderBy('order_index')->get()->groupBy(fn ($t) => $t->status->value);
        $statuses = \App\Enums\TaskStatus::cases();

        return view('sprints.show', compact('project', 'sprint', 'tasks', 'statuses'));
    }

    public function edit(Project $project, Sprint $sprint)
    {
        $this->authorize('update', $project);
        abort_if($sprint->project_id != $project->id, 404);

        $statuses = SprintStatus::cases();
        return view('sprints.edit', compact('project', 'sprint', 'statuses'));
    }

    public function update(UpdateSprintRequest $request, Project $project, Sprint $sprint)
    {
        $this->authorize('update', $project);
        abort_if($sprint->project_id != $project->id, 404);

        // Enforce one active sprint per project
        if ($request->status === 'active') {
            $project->sprints()
                ->where('status', 'active')
                ->where('id', '!=', $sprint->id)
                ->update(['status' => 'planned']);
        }

        $sprint->update($request->validated());

        return redirect()->route('projects.sprints.show', [$project, $sprint])
            ->with('success', 'Raid updated.');
    }

    public function destroy(Project $project, Sprint $sprint)
    {
        $this->authorize('update', $project);
        abort_if($sprint->project_id != $project->id, 404);

        $sprint->tasks()->update(['sprint_id' => null]);
        $sprint->delete();

        return redirect()->route('projects.sprints.index', $project)
            ->with('success', 'Raid deleted. Quests moved to backlog.');
    }
}
