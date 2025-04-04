<?php

namespace App\Filament\Resources\ChapterResource\Pages;

use App\Filament\Resources\ChapterResource;
use App\Filament\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChapter extends EditRecord
{
    protected static string $resource = ChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make("view_unit")->url(fn($record) => UnitResource::getUrl("edit", ['record' => $record->unit->first()]))->label(fn($record) => __("custom.models.unit.action.details") . "'" . $record->unit->first()->name . "'"),

        ];
    }
}
