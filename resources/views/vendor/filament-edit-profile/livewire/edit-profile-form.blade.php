<x-filament-panels::form wire:submit="updateProfile">
    {{ $this->form }}

    <div class="fi-form-actions">
        <div class="flex flex-row-reverse flex-wrap items-center gap-3 fi-ac">
            <x-filament::button type="submit">
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target='updateProfile' />
                {{ __('filament-edit-profile::default.save') }}
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::form>
