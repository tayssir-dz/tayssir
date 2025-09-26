@props(['name', 'avatar_url' => null])

<div {{ $attributes->class(['filament-avatar flex items-center justify-center']) }}>
    <img
        src="{{ $avatar_url }}"
        alt="{{ $name }}"
        class="w-10 h-10 rounded-full ring-2 dark:ring-gray-500 ring-gray-300"
    />
</div>
