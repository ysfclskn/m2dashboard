<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);

        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $uploadedFile = $request->file('file');
        $ext          = $uploadedFile->getClientOriginalExtension();
        $safeName     = 'task_' . $task->id . '_' . time() . '_' . Str::random(8) . '.' . strtolower($ext);
        $originalName = basename($uploadedFile->getClientOriginalName());
        $mimeType     = $uploadedFile->getClientMimeType();
        $fileSize     = $uploadedFile->getSize();

        $dir = $this->uploadPath('uploads/task-attachments');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $uploadedFile->move($dir, $safeName);

        $task->attachments()->create([
            'project_id'    => $project->id,
            'uploaded_by'   => auth()->id(),
            'original_name' => $originalName,
            'file_name'     => $safeName,
            'file_path'     => 'uploads/task-attachments/' . $safeName,
            'mime_type'     => $mimeType,
            'size'          => $fileSize,
        ]);

        return back()->with('success', 'Attachment uploaded.');
    }

    public function destroy(Project $project, Task $task, Attachment $attachment)
    {
        $this->authorize('manageTasks', $project);
        abort_if($task->project_id != $project->id, 404);
        abort_if($attachment->attachable_type !== Task::class || (int) $attachment->attachable_id !== (int) $task->id, 404);

        $path = $this->uploadPath($attachment->file_path);
        if (file_exists($path)) {
            unlink($path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment deleted.');
    }

    private function uploadPath(string $relative): string
    {
        // Production: app lives in dash4game-app/, web root is public_html/ (sibling folder)
        $sibling = dirname(base_path()) . '/public_html';
        $publicHtml = is_dir($sibling) ? $sibling : public_path();
        return rtrim($publicHtml, '/') . '/' . ltrim($relative, '/');
    }
}
