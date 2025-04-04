<?php

namespace App\Filament\Resources\UnitResource\Pages;

use App\Filament\Resources\MaterialResource;
use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            // Action to navigate to a specific link (/ for example)
            Actions\Action::make("view_material")
                ->url(fn($record) => MaterialResource::getUrl("edit", ['record' => $record->material->first()]))
                ->label(fn($record) => __("custom.models.material.action.details") . " '" . ($record->material->first()->name ?? 'N/A') . "'"),
        ];
    }
}
