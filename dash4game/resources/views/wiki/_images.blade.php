{{-- Wiki Images Panel — included in show and edit --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mt-4">
    <h2 class="text-sm font-semibold text-gray-300 mb-4">🖼️ Wiki Images</h2>

    @if ($wikiPage->attachments->isNotEmpty())
        <div class="space-y-2 mb-5">
            @foreach ($wikiPage->attachments as $img)
                <div class="flex items-center gap-3 bg-gray-800 rounded-lg p-2">
                    <a href="{{ $img->url() }}" target="_blank">
                        <img src="{{ $img->url() }}" alt="{{ $img->original_name }}"
                             class="w-16 h-16 object-cover rounded hover:opacity-80 transition-opacity shrink-0">
                    </a>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-300 truncate">{{ $img->original_name }}</p>
                        <p class="text-xs text-gray-600 mb-1">{{ $img->formattedSize() }}</p>
                        <div class="flex items-center gap-1">
                            <input type="text" readonly
                                   value="![{{ $img->original_name }}]({{ $img->url() }})"
                                   class="flex-1 text-xs bg-gray-900 border border-gray-700 text-gray-400 rounded px-2 py-1 font-mono focus:outline-none select-all"
                                   onclick="this.select()">
                            <button type="button"
                                    onclick="navigator.clipboard.writeText(this.previousElementSibling.value).then(()=>{ this.textContent='✓'; setTimeout(()=>this.textContent='Copy',1500); })"
                                    class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded hover:bg-gray-600 transition-colors shrink-0">
                                Copy
                            </button>
                        </div>
                    </div>
                    @can('manageWiki', $project)
                        <form method="POST"
                              action="{{ route('projects.wiki.images.destroy', [$project, $wikiPage, $img]) }}"
                              onsubmit="return confirm('Delete this image?')" class="shrink-0">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-400 px-1">✕</button>
                        </form>
                    @endcan
                </div>
            @endforeach
        </div>
    @else
        <p class="text-xs text-gray-600 mb-4">No images uploaded yet.</p>
    @endif

    @can('manageWiki', $project)
        @error('file')
            <p class="text-xs text-red-400 mb-2">⚠️ {{ $message }}</p>
        @enderror
        <form method="POST"
              action="{{ route('projects.wiki.images.store', [$project, $wikiPage]) }}"
              enctype="multipart/form-data"
              class="flex items-center gap-3">
            @csrf
            <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/gif"
                   class="text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                          file:text-xs file:font-medium file:bg-gray-700 file:text-gray-200
                          hover:file:bg-gray-600 cursor-pointer">
            <button type="submit"
                    class="px-3 py-1.5 bg-amber-500 text-gray-900 font-semibold text-xs rounded-lg hover:bg-amber-400 transition-colors shrink-0">
                Upload
            </button>
        </form>
        <p class="text-xs text-gray-600 mt-2">JPG, PNG, WebP, GIF · max 4 MB</p>
    @endcan
</div>
