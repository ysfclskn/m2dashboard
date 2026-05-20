<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgressReportRequest;
use App\Http\Requests\UpdateProgressReportRequest;
use App\Models\ProgressReport;
use App\Models\Project;

class ProjectProgressReportController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $reports = $project->progressReports()->with('creator')->get();

        return view('reports.index', compact('project', 'reports'));
    }

    public function create(Project $project)
    {
        $this->authorize('update', $project);

        return view('reports.create', compact('project'));
    }

    public function store(StoreProgressReportRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $project->progressReports()->create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('projects.reports.index', $project)
            ->with('success', 'Adventure report created!');
    }

    public function show(Project $project, ProgressReport $progressReport)
    {
        $this->authorize('view', $project);
        abort_if($progressReport->project_id != $project->id, 404);

        $progressReport->load('creator');

        return view('reports.show', compact('project', 'progressReport'));
    }

    public function edit(Project $project, ProgressReport $progressReport)
    {
        $this->authorize('update', $project);
        abort_if($progressReport->project_id != $project->id, 404);

        return view('reports.edit', compact('project', 'progressReport'));
    }

    public function update(UpdateProgressReportRequest $request, Project $project, ProgressReport $progressReport)
    {
        $this->authorize('update', $project);
        abort_if($progressReport->project_id != $project->id, 404);

        $progressReport->update($request->validated());

        return redirect()->route('projects.reports.show', [$project, $progressReport])
            ->with('success', 'Report updated.');
    }

    public function destroy(Project $project, ProgressReport $progressReport)
    {
        $this->authorize('update', $project);
        abort_if($progressReport->project_id != $project->id, 404);

        $progressReport->delete();

        return redirect()->route('projects.reports.index', $project)
            ->with('success', 'Report deleted.');
    }
}
