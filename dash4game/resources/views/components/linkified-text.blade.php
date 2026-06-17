@props(['text' => null])
<span class="whitespace-pre-wrap break-words">{!! \App\Support\Linkify::text($text) !!}</span>
