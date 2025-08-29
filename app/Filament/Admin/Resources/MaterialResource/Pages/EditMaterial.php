<?php

namespace App\Filament\Admin\Resources\MaterialResource\Pages;

use App\Filament\Admin\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;

class EditMaterial extends EditRecord
{
    use HasRecordSwitcher;

    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
