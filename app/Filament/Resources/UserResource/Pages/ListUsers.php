<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $arr = [];
        if (auth()->user()->can("view_all_user")) {
            $arr['all'] = Tab::make()->label(__('custom.models.user.tabs.all'));
        }
        if (auth()->user()->can("view_students_user")) {
            $arr["students"] = Tab::make()->icon("heroicon-o-users")->label(__('custom.models.user.tabs.students'))
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', 'student');
                    });
                });
        }
        if (auth()->user()->can("view_with_roles_user")) {
            $arr["with roles"] = Tab::make()->label(__('custom.models.user.tabs.with_roles'))
                ->icon("heroicon-o-user-group")
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('roles', function ($query) {
                        $query->where('name', '!=', 'student');
                    });
                });
        }
        return $arr;
    }
}
