<?php

namespace App\Filament\Admin\Resources\DivisionResource\Pages;

use App\Filament\Admin\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;

class EditDivision extends EditRecord
{
    use HasRecordSwitcher;

    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
