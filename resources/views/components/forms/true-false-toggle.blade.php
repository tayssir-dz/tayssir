@php
    // Force boolean value, default to false if null
    $isCorrect = (bool) ($getState() ?? false);
@endphp

<div class="flex items-center">
    <div class="inline-flex gap-3 shadow-sm" role="group">
        <x-filament::button size="sm" wire:click="$set('{{ $getStatePath() }}', true)" :color="$isCorrect ? 'success' : 'gray'"
            class="rounded-r-none" type="button">
            <x-filament::icon name="heroicon-m-check" class="w-5 h-5 mr-1" />
            {{ __('custom.models.question.true_false.true') }}
        </x-filament::button>

        <x-filament::button size="sm" wire:click="$set('{{ $getStatePath() }}', false)" :color="!$isCorrect ? 'danger' : 'gray'"
            class="rounded-l-none" type="button">
            <x-filament::icon name="heroicon-m-x-mark" class="w-5 h-5 mr-1" />
            {{ __('custom.models.question.true_false.false') }}
        </x-filament::button>
    </div>
</div>
