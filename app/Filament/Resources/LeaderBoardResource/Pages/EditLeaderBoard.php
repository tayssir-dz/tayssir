<?php

namespace App\Filament\Resources\LeaderBoardResource\Pages;

use App\Filament\Resources\LeaderBoardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeaderBoard extends EditRecord
{
    protected static string $resource = LeaderBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
