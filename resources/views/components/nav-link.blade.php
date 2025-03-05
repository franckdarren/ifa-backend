@props(['active' => false, 'icone' => null])

@php
    $classes = $active
        ? 'flex items-center px-6 py-3 mt-4 text-[#4996d1] border-[#4996d1] border-l-4 '
        : 'flex items-center px-2 mx-4 py-2 mt-4 text-gray-100 hover:bg-[#4996d1] hover:rounded-md hover:bg-opacity-35 hover:text-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} wire:navigate>
    @if ($icone)
        {!! $icone !!}
    @endif
    <span class="mx-3">{{ $slot }}</span>
</a>
