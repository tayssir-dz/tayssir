@props(['name', 'avatar_url' => null])

@php
    $initials = collect(explode(' ', $name))
        ->map(fn ($segment) => mb_substr($segment, 0, 1))
        ->join('');

    $avatarUrl = $avatar_url
        ? Storage::url($avatar_url)
        : "https://ui-avatars.com/api/?name=" . urlencode($name) . "&color=FFFFFF&background=111827";
@endphp

<div {{ $attributes->class(['filament-avatar flex items-center justify-center']) }}>
    <img
        src="{{ $avatarUrl }}"
        alt="{{ $name }}"
        class="w-10 h-10 rounded-full ring-2 dark:ring-gray-500 ring-gray-300"
    />
</div>
