<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('send_notification_to_all_users')
            ->label("global notification")
            ->icon('heroicon-o-bell')
            ->action(function () {
                // chunk and notify
                User::chunk(100, function ($users) {
                    foreach ($users as $user) {
                        $user->notify()
                    }
                });
            })
        ];
    }

    public function getTabs(): array
    {
        $arr = [];

        $arr['all'] = Tab::make()->label(__('custom.models.user.tabs.all'));

        $arr['students'] = Tab::make()->icon('heroicon-o-users')->label(__('custom.models.user.tabs.students'))
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'student');
                });
            });

        $arr['with roles'] = Tab::make()->label(__('custom.models.user.tabs.with_roles'))
            ->icon('heroicon-o-user-group')
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('name', '!=', 'student');
                });
            });


        $arr['google_users'] = Tab::make()->label(__('custom.models.user.tabs.google_users'))
            ->icon('heroicon-o-user-group')
            ->modifyQueryUsing(function (Builder $query) {
                $query->googleUsers();
            });


        $arr['scoped'] = Tab::make()->label(__('custom.models.user.tabs.scoped'))
            ->icon('heroicon-o-user-group')
            ->modifyQueryUsing(function (Builder $query) {
                $query->withinPlatformPeriod();
            });
        return $arr;
    }
}
