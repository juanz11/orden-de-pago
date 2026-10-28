@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'bg-gradient-to-r from-blue-600 to-blue-500 text-white shadow-lg shadow-blue-950/40'
            : 'text-blue-200/70 hover:text-white hover:bg-white/10';
@endphp

<a {{ $attributes->merge(['class' => 'group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 ' . $classes]) }}>
    @isset($icon)
        <span class="shrink-0 {{ $active ? 'text-white' : 'text-blue-300/60 group-hover:text-blue-200' }} transition-colors duration-150">
            {{ $icon }}
        </span>
    @endisset
    <span class="truncate">{{ $slot }}</span>
    @if($active)
        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-300 shadow shadow-sky-300/50"></span>
    @endif
</a>
