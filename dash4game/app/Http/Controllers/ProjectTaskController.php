<?php

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;

class ProjectTaskController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $project->tasks()
            ->with(['assignee', 'sprint'])
            ->orderBy('order_index')
            ->get()
            ->groupBy(fn ($t) => $t->status->value);

        $statuses = TaskStatus::cases();
        $members  = $project->members()->with('user')->get();
        $sprints  = $project->sprints()->whereIn('status', ['planned', 'active'])->get();

        return view('tasks.index', compact('project', 'tasks', 'statuses', 'members', 'sprints'));
    }

    public function create(Project $project)
    {
        $this->authorize('manageTasks', $project);

        $types     = TaskType::cases();
        $statuses  = TaskStatus::cases();
        $priorities = TaskPriority::cases();
        $members   = $project->members()->with('user')->get();
        $sprints   = $project->sprints()->whereIn('status', ['planned', 'active'])->get();

        return view('tasks.create', compact('project', 'types', 'statuses', 'priorities', 'members', 'sprints'));
    }

    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('manageTasks', $project);

        $maxOrder = $project->tasks()->max('order_index') ?? -1;

        $project->tasks()->create([
            ...$request->validated(),
            'created_by'  => auth()->id(),
            'order_index' => $maxOrder + 1,
        ]);

        return redirect()->route('projects.quests.index', $project)
            ->with('success', 'Quest created!');
    }

    public function show(Project $project, Task $task)
    {
        $this->authorize('view', $project);
        abort_if($task->project_id != $project->id, 404);

        $task->load(['assignee', 'creator', 'sprint']);

        return view('tasks.show', compact('project', 'task'));
    }

    public function edit(Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);

        $types      = TaskType::cases();
        $statuses   = TaskStatus::cases();
        $priorities = TaskPriority::cases();
        $members    = $project->members()->with('user')->get();
        $sprints    = $project->sprints()->whereIn('status', ['planned', 'active'])->get();

        return view('tasks.edit', compact('project', 'task', 'types', 'statuses', 'priorities', 'members', 'sprints'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);

        $task->update($request->validated());

        return redirect()->route('projects.quests.show', [$project, $task])
            ->with('success', 'Quest updated.');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Quest status updated.');
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);

        $task->delete();

        return redirect()->route('projects.quests.index', $project)
            ->with('success', 'Quest deleted.');
    }
}
