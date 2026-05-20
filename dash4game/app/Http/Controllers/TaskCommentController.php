<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorize('view', $project);
        abort_if($task->project_id != $project->id, 403);

        $member = $project->members()->where('user_id', auth()->id())->first();
        abort_if(!$member || $member->role->value === 'viewer', 403);

        $request->validate(['body' => 'required|string|max:2000']);

        $task->comments()->create([
            'project_id' => $project->id,
            'user_id'    => auth()->id(),
            'body'       => $request->body,
        ]);

        return back()->with('success', 'Comment added.');
    }

    public function destroy(Project $project, Task $task, Comment $comment)
    {
        $this->authorize('view', $project);
        abort_if($task->project_id != $project->id, 403);
        abort_if($comment->commentable_type !== Task::class || (int) $comment->commentable_id !== (int) $task->id, 403);

        $user   = auth()->user();
        $member = $project->members()->where('user_id', $user->id)->first();

        $canDelete = $comment->user_id === $user->id
            || ($member && in_array($member->role->value, ['owner', 'admin']));

        abort_unless($canDelete, 403);

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
