<?php

namespace App\Filament\Resources\LeaderBoardResource\Pages;

use App\Filament\Resources\LeaderBoardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLeaderBoards extends ListRecords
{
    protected static string $resource = LeaderBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
