@props(['href', 'active' => false])
<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium transition duration-200 ' . ($active
            ? 'bg-white/15 text-white shadow-inner ring-1 ring-white/10'
            : 'text-emerald-100/85 hover:bg-white/10 hover:text-white'),
    ]) }}
>
    {{ $slot }}
</a>
