<?php

namespace App\Filament\Admin\Resources\FlashcardGroupResource\Pages;

use App\Filament\Admin\Resources\FlashcardGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlashcardGroup extends ListRecords
{
    protected static string $resource = FlashcardGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
