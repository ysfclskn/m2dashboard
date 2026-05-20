<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ProjectMemberController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $members = $project->members()->with('user')->get();
        $roles   = ProjectRole::cases();

        return view('members.index', compact('project', 'members', 'roles'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role'  => ['required', new Enum(ProjectRole::class)],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($project->owner_id === $user->id) {
            return back()->with('error', 'This user is the project owner.');
        }

        if ($project->members()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'This user is already a member.');
        }

        $project->members()->create([
            'user_id' => $user->id,
            'role'    => $request->role,
        ]);

        return back()->with('success', $user->name . ' joined the party!');
    }

    public function update(Request $request, Project $project, ProjectMember $projectMember)
    {
        $this->authorize('manageMembers', $project);
        abort_if($projectMember->project_id != $project->id, 404);

        if ($projectMember->role === ProjectRole::Owner) {
            return back()->with('error', 'Cannot change the owner\'s role.');
        }

        $request->validate(['role' => ['required', new Enum(ProjectRole::class)]]);

        if ($request->role === ProjectRole::Owner->value) {
            return back()->with('error', 'Cannot assign owner role.');
        }

        $projectMember->update(['role' => $request->role]);

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Project $project, ProjectMember $projectMember)
    {
        $this->authorize('manageMembers', $project);
        abort_if($projectMember->project_id != $project->id, 404);

        if ($projectMember->role === ProjectRole::Owner) {
            return back()->with('error', 'Cannot remove the project owner.');
        }

        $projectMember->delete();

        return back()->with('success', 'Member removed from party.');
    }
}
