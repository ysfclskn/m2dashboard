@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-400 bg-green-900/30 px-3 py-2 rounded-lg']) }}>
        {{ $status }}
    </div>
@endif
