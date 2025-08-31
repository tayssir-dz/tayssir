<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Howdu\FilamentRecordSwitcher\Filament\Concerns\HasRecordSwitcher;

class EditUser extends EditRecord
{
    use HasRecordSwitcher;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        $routeName = static::getRouteName();
        $currentRoute = request()->route()->getName();
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
