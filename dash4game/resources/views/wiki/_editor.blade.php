@php
$fieldName = $fieldName ?? 'content';
$fieldLabel = $fieldLabel ?? 'Content';
$rows = $rows ?? 18;
@endphp
<div x-data="{ tab: 'write' }">

    {{-- Label + Tab toggle --}}
    <div class="flex items-center justify-between mb-1">
        <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-300">{{ $fieldLabel }}</label>
        <div class="flex text-xs border border-gray-700 rounded overflow-hidden">
            <button type="button"
                    @click="tab='write'"
                    :class="tab==='write' ? 'bg-gray-700 text-gray-200' : 'text-gray-500 hover:text-gray-300'"
                    class="px-3 py-1 transition-colors">✏️ Edit</button>
            <button type="button"
                    @click="tab='preview'; $nextTick(() => renderMdPreview('{{ $fieldName }}', 'md-preview-{{ $fieldName }}'))"
                    :class="tab==='preview' ? 'bg-gray-700 text-gray-200' : 'text-gray-500 hover:text-gray-300'"
                    class="px-3 py-1 border-l border-gray-700 transition-colors">👁 Preview</button>
        </div>
    </div>

    {{-- Toolbar --}}
    <div x-show="tab==='write'"
         class="flex flex-wrap gap-1 px-2 py-1.5 bg-gray-800 border border-gray-700 border-b-0 rounded-t-lg">
        <button type="button" onclick="mdWrap('**','**', '{{ $fieldName }}')" title="Bold" class="md-btn font-bold">B</button>
        <button type="button" onclick="mdWrap('*','*', '{{ $fieldName }}')" title="Italic" class="md-btn italic">I</button>
        <button type="button" onclick="mdWrap('~~','~~', '{{ $fieldName }}')" title="Strikethrough" class="md-btn line-through">S</button>
        <span class="self-center text-gray-700">|</span>
        <button type="button" onclick="mdLine('# ', '{{ $fieldName }}')" title="Heading 1" class="md-btn">H1</button>
        <button type="button" onclick="mdLine('## ', '{{ $fieldName }}')" title="Heading 2" class="md-btn">H2</button>
        <button type="button" onclick="mdLine('### ', '{{ $fieldName }}')" title="Heading 3" class="md-btn">H3</button>
        <span class="self-center text-gray-700">|</span>
        <button type="button" onclick="mdLine('- ', '{{ $fieldName }}')" title="Bullet list" class="md-btn">• List</button>
        <button type="button" onclick="mdLine('1. ', '{{ $fieldName }}')" title="Numbered list" class="md-btn">1. List</button>
        <button type="button" onclick="mdLine('> ', '{{ $fieldName }}')" title="Blockquote" class="md-btn">❝</button>
        <span class="self-center text-gray-700">|</span>
        <button type="button" onclick="mdWrap('`','`', '{{ $fieldName }}')" title="Inline code" class="md-btn font-mono">code</button>
        <button type="button" onclick="mdCodeBlock('{{ $fieldName }}')" title="Code block" class="md-btn font-mono">```</button>
        <button type="button" onclick="mdLine('---\n', '{{ $fieldName }}')" title="Horizontal rule" class="md-btn">—</button>
        <span class="self-center text-gray-700">|</span>
        <button type="button" onclick="mdInsertLink('{{ $fieldName }}')" title="Insert link" class="md-btn">🔗 Link</button>
        <button type="button" onclick="mdInsertImage('{{ $fieldName }}')" title="Insert image markdown" class="md-btn">🖼 Image</button>
    </div>

    {{-- Textarea --}}
    <textarea x-show="tab==='write'"
              id="{{ $fieldName }}" name="{{ $fieldName }}" rows="{{ $rows }}"
              class="w-full bg-gray-800 border border-gray-700 rounded-b-lg text-gray-100 px-3 py-2 text-sm font-mono focus:border-amber-500 focus:ring-1 focus:ring-amber-500 focus:outline-none">{{ $initialContent }}</textarea>

    {{-- Preview pane --}}
    <div x-show="tab==='preview'" x-cloak
         id="md-preview-{{ $fieldName }}"
         class="wiki-prose min-h-72 bg-gray-800 border border-gray-700 rounded-lg px-5 py-4">
        <p class="text-gray-500 text-xs italic">Loading preview…</p>
    </div>

    <p class="text-xs text-gray-600 mt-1">Supports Markdown: **bold**, *italic*, # headings, - lists, `code`, [link](url)</p>
</div>

@push('styles')
<style>
.md-btn{padding:2px 8px;font-size:.75rem;background:#374151;color:#d1d5db;border-radius:.25rem;cursor:pointer;transition:background .15s}
.md-btn:hover{background:#4b5563}
.wiki-prose h1{font-size:1.5rem;font-weight:700;color:#f1f5f9;margin:1.5rem 0 .75rem;padding-bottom:.5rem;border-bottom:1px solid #1f2937}
.wiki-prose h2{font-size:1.25rem;font-weight:700;color:#f1f5f9;margin:1.25rem 0 .5rem}
.wiki-prose h3{font-size:1.1rem;font-weight:600;color:#e2e8f0;margin:1rem 0 .4rem}
.wiki-prose h4{font-size:1rem;font-weight:600;color:#e2e8f0;margin:.75rem 0 .3rem}
.wiki-prose p{margin-bottom:.75rem;line-height:1.75;color:#d1d5db}
.wiki-prose ul{list-style-type:disc;padding-left:1.5rem;margin-bottom:.75rem}
.wiki-prose ol{list-style-type:decimal;padding-left:1.5rem;margin-bottom:.75rem}
.wiki-prose li{margin-bottom:.25rem;color:#d1d5db}
.wiki-prose a{color:#fbbf24;text-decoration:underline}
.wiki-prose a:hover{color:#fcd34d}
.wiki-prose code{background:#111827;color:#fcd34d;padding:.1rem .35rem;border-radius:.25rem;font-size:.8rem;font-family:monospace}
.wiki-prose pre{background:#111827;border:1px solid #374151;border-radius:.5rem;padding:1rem;overflow-x:auto;margin-bottom:.75rem}
.wiki-prose pre code{background:transparent;color:#d1d5db;padding:0}
.wiki-prose blockquote{border-left:4px solid rgba(251,191,36,.35);padding-left:1rem;color:#9ca3af;font-style:italic;margin-bottom:.75rem}
.wiki-prose hr{border-color:#374151;margin:1.25rem 0}
.wiki-prose img{max-width:100%;border-radius:.5rem;margin:.75rem 0}
.wiki-prose strong{font-weight:700;color:#f1f5f9}
.wiki-prose em{font-style:italic;color:#d1d5db}
.wiki-prose table{width:100%;border-collapse:collapse;margin-bottom:.75rem;font-size:.875rem}
.wiki-prose th{border:1px solid #374151;padding:.4rem .75rem;text-align:left;background:#1f2937;color:#f1f5f9;font-weight:600}
.wiki-prose td{border:1px solid #374151;padding:.4rem .75rem;color:#d1d5db}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked@9/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3/dist/purify.min.js"></script>
<script>
if (typeof mdWrap === 'undefined') {
    function mdWrap(pre, post, id) {
        const ta = document.getElementById(id); if (!ta) return;
        const s = ta.selectionStart, e = ta.selectionEnd;
        const sel = ta.value.substring(s, e) || 'text';
        ta.setRangeText(pre + sel + post, s, e, 'select');
        ta.focus();
    }

    function mdLine(prefix, id) {
        const ta = document.getElementById(id); if (!ta) return;
        const s = ta.selectionStart;
        const lineStart = ta.value.lastIndexOf('\n', s - 1) + 1;
        ta.setRangeText(prefix, lineStart, lineStart, 'start');
        ta.focus();
    }

    function mdCodeBlock(id) {
        const ta = document.getElementById(id); if (!ta) return;
        const s = ta.selectionStart, e = ta.selectionEnd;
        const sel = ta.value.substring(s, e) || 'code';
        ta.setRangeText('```\n' + sel + '\n```', s, e, 'select');
        ta.focus();
    }

    function mdInsertLink(id) {
        const url = prompt('URL:', 'https://');
        if (!url) return;
        const ta = document.getElementById(id); if (!ta) return;
        const s = ta.selectionStart, e = ta.selectionEnd;
        const text = ta.value.substring(s, e) || 'link text';
        ta.setRangeText('[' + text + '](' + url + ')', s, e, 'end');
        ta.focus();
    }

    function mdInsertImage(id) {
        const url = prompt('Image URL:', '/uploads/wiki-images/');
        if (!url) return;
        const ta = document.getElementById(id); if (!ta) return;
        const alt = ta.value.substring(ta.selectionStart, ta.selectionEnd) || 'image';
        const s = ta.selectionStart, e = ta.selectionEnd;
        ta.setRangeText('![' + alt + '](' + url + ')', s, e, 'end');
        ta.focus();
    }

    function renderMdPreview(id, previewId) {
        const ta = document.getElementById(id);
        const preview = document.getElementById(previewId);
        if (!ta || !preview || typeof marked === 'undefined') return;
        const html = typeof DOMPurify !== 'undefined'
            ? DOMPurify.sanitize(marked.parse(ta.value || ''))
            : marked.parse(ta.value || '');
        preview.innerHTML = html || '<p style="color:#6b7280;font-style:italic;font-size:.875rem">Nothing to preview yet.</p>';
    }
}
</script>
@endpush
