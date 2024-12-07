@php
    $isCorrect = $getState() ?? false;
@endphp

<div class="flex items-center">
    <div class="inline-flex gap-3 shadow-sm" role="group">
        <x-filament::button size="sm" wire:click="$set('{{ $getStatePath() }}', true)" :color="$isCorrect ? 'success' : 'gray'"
            class="rounded-r-none">
            {{ __('custom.models.question.true_false.true') }}
        </x-filament::button>

        <x-filament::button size="sm" wire:click="$set('{{ $getStatePath() }}', false)" :color="!$isCorrect ? 'danger' : 'gray'"
            class="rounded-l-none">
            {{ __('custom.models.question.true_false.false') }}
        </x-filament::button>
    </div>
</div>
