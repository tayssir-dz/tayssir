@props(['name', 'avatar_url' => null])

<div {{ $attributes->class(['filament-avatar flex items-center justify-center']) }}>
    @if ($avatar_url)
        <img
            src="{{ $avatar_url }}"
            alt="{{ $name }}"
            class="w-10 h-10 rounded-full ring-2 dark:ring-gray-500 ring-gray-300"
        />
    @else
        @php
            $initial = function_exists('mb_substr')
                ? mb_strtoupper(mb_substr((string) $name, 0, 1))
                : strtoupper(substr((string) $name, 0, 1));
        @endphp
        <div class="w-10 h-10 rounded-full ring-2 dark:ring-gray-500 ring-gray-300 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 flex items-center justify-center">
            <span class="font-semibold">{{ $initial }}</span>
        </div>
    @endif
</div>
