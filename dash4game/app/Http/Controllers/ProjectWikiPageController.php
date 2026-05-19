<?php

namespace App\Http\Controllers;

use App\Enums\WikiPageCategory;
use App\Http\Requests\StoreWikiPageRequest;
use App\Http\Requests\UpdateWikiPageRequest;
use App\Models\Project;
use App\Models\WikiPage;
use Illuminate\Support\Str;

class ProjectWikiPageController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $pages = $project->wikiPages()
            ->with('creator')
            ->orderBy('category')
            ->orderBy('title')
            ->get()
            ->groupBy(fn ($p) => $p->category->value);

        $categories = WikiPageCategory::cases();

        return view('wiki.index', compact('project', 'pages', 'categories'));
    }

    public function create(Project $project)
    {
        $this->authorize('manageWiki', $project);

        $categories = WikiPageCategory::cases();
        return view('wiki.create', compact('project', 'categories'));
    }

    public function store(StoreWikiPageRequest $request, Project $project)
    {
        $this->authorize('manageWiki', $project);

        $slug = Str::slug($request->title);
        $base = $slug;
        $i = 1;
        while ($project->wikiPages()->withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $project->wikiPages()->create([
            ...$request->validated(),
            'created_by' => auth()->id(),
            'slug'       => $slug,
        ]);

        return redirect()->route('projects.wiki.index', $project)
            ->with('success', 'Wiki page created!');
    }

    public function show(Project $project, WikiPage $wikiPage)
    {
        $this->authorize('view', $project);
        abort_if($wikiPage->project_id !== $project->id, 404);

        $wikiPage->load('creator', 'updater');

        return view('wiki.show', compact('project', 'wikiPage'));
    }

    public function edit(Project $project, WikiPage $wikiPage)
    {
        $this->authorize('manageWiki', $project);
        abort_if($wikiPage->project_id !== $project->id, 404);

        $categories = WikiPageCategory::cases();
        return view('wiki.edit', compact('project', 'wikiPage', 'categories'));
    }

    public function update(UpdateWikiPageRequest $request, Project $project, WikiPage $wikiPage)
    {
        $this->authorize('manageWiki', $project);
        abort_if($wikiPage->project_id !== $project->id, 404);

        $wikiPage->update([
            ...$request->validated(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('projects.wiki.show', [$project, $wikiPage])
            ->with('success', 'Wiki page updated.');
    }

    public function destroy(Project $project, WikiPage $wikiPage)
    {
        $this->authorize('manageWiki', $project);
        abort_if($wikiPage->project_id !== $project->id, 404);

        $wikiPage->delete();

        return redirect()->route('projects.wiki.index', $project)
            ->with('success', 'Wiki page deleted.');
    }
}
