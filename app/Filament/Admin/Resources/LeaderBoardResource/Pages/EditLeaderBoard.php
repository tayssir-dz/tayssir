<?php

namespace App\Filament\Admin\Resources\LeaderBoardResource\Pages;

use App\Filament\Admin\Resources\LeaderBoardResource;
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
