<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $myProjects = Project::where('owner_id', $user->id)
            ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id))
            ->withCount('tasks')
            ->latest()
            ->take(5)
            ->get();

        $myTasks = Task::where('assignee_id', $user->id)
            ->whereNotIn('status', ['done'])
            ->with('project')
            ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
            ->take(8)
            ->get();

        return view('dashboard', compact('myProjects', 'myTasks'));
    }
}
