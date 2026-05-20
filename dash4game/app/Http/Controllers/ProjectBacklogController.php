<?php

namespace App\Http\Controllers;

use App\Enums\SprintStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectBacklogController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $backlogTasks = $project->tasks()
            ->whereNull('sprint_id')
            ->with('assignee')
            ->orderBy('priority')
            ->orderBy('order_index')
            ->get();

        $activeSprint = $project->sprints()
            ->where('status', SprintStatus::Active)
            ->with(['tasks' => fn ($q) => $q->with('assignee')->orderBy('priority')->orderBy('order_index')])
            ->first();

        $plannedSprints = $project->sprints()
            ->where('status', SprintStatus::Planned)
            ->withCount('tasks')
            ->with(['tasks' => fn ($q) => $q->with('assignee')->orderBy('priority')->orderBy('order_index')])
            ->orderBy('start_date')
            ->get();

        // For the sprint assignment dropdown
        $assignableSprints = $project->sprints()
            ->whereIn('status', [SprintStatus::Active, SprintStatus::Planned])
            ->orderByRaw("FIELD(status, 'active', 'planned')")
            ->get();

        return view('backlog.index', compact(
            'project', 'backlogTasks', 'activeSprint', 'plannedSprints', 'assignableSprints'
        ));
    }

    public function updateSprint(Request $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);

        $request->validate([
            'sprint_id' => ['nullable', 'integer'],
        ]);

        if ($request->sprint_id) {
            $sprint = $project->sprints()
                ->whereIn('status', [SprintStatus::Active, SprintStatus::Planned])
                ->findOrFail($request->sprint_id);
        }

        $task->update(['sprint_id' => $request->sprint_id]);

        return back()->with('success', 'Quest assignment updated.');
    }
}
