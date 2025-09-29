<?php

namespace App\Filament\Admin\Resources\LeaderBoardResource\Pages;

use App\Filament\Admin\Resources\LeaderBoardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeaderBoards extends ListRecords
{
    protected static string $resource = LeaderBoardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $arr = [];

        $arr['all'] = Tab::make()->label(__('custom.tabs.all'));

        $arr['scoped'] = Tab::make()->label(__('custom.models.leaderboard.tabs.scoped'))
            ->modifyQueryUsing(function (Builder $query) {
                $query->withinPlatformPeriod();
            });
        return $arr;
    }
}
