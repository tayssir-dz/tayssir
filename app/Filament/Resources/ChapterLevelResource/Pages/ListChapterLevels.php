<?php

namespace App\Filament\Resources\ChapterLevelResource\Pages;

use App\Filament\Resources\ChapterLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChapterLevels extends ListRecords
{
    protected static string $resource = ChapterLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
