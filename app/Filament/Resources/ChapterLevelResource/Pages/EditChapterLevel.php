<?php

namespace App\Filament\Resources\ChapterLevelResource\Pages;

use App\Filament\Resources\ChapterLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChapterLevel extends EditRecord
{
    protected static string $resource = ChapterLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
