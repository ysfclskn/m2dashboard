<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\WikiPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WikiImageController extends Controller
{
    public function store(Request $request, Project $project, WikiPage $wikiPage)
    {
        $this->authorize('manageWiki', $project);
        abort_if($wikiPage->project_id != $project->id, 404);

        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
        ]);

        $uploaded     = $request->file('file');
        $ext          = $uploaded->getClientOriginalExtension();
        $originalName = basename($uploaded->getClientOriginalName());
        $mimeType     = $uploaded->getClientMimeType();
        $fileSize     = $uploaded->getSize();
        $safeName     = 'wiki_' . $wikiPage->id . '_' . time() . '_' . Str::random(8) . '.' . strtolower($ext);

        $dir = $this->uploadPath('uploads/wiki-images');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $uploaded->move($dir, $safeName);

        $wikiPage->attachments()->create([
            'project_id'    => $project->id,
            'uploaded_by'   => auth()->id(),
            'original_name' => $originalName,
            'file_name'     => $safeName,
            'file_path'     => 'uploads/wiki-images/' . $safeName,
            'mime_type'     => $mimeType,
            'size'          => $fileSize,
        ]);

        return back()->with('success', 'Image uploaded.');
    }

    public function destroy(Project $project, WikiPage $wikiPage, Attachment $attachment)
    {
        $this->authorize('manageWiki', $project);
        abort_if($wikiPage->project_id != $project->id, 404);
        abort_if($attachment->attachable_type !== WikiPage::class || (int) $attachment->attachable_id !== (int) $wikiPage->id, 404);

        $path = $this->uploadPath($attachment->file_path);
        if (file_exists($path)) {
            unlink($path);
        }

        $attachment->delete();

        return back()->with('success', 'Image deleted.');
    }

    private function uploadPath(string $relative): string
    {
        // Production: app lives in dash4game-app/, web root is public_html/ (sibling folder)
        $sibling = dirname(base_path()) . '/public_html';
        $publicHtml = is_dir($sibling) ? $sibling : public_path();
        return rtrim($publicHtml, '/') . '/' . ltrim($relative, '/');
    }
}
