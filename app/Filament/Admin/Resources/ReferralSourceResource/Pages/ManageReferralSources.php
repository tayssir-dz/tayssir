<?php

namespace App\Filament\Admin\Resources\ReferralSourceResource\Pages;

use App\Filament\Admin\Resources\ReferralSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageReferralSources extends ManageRecords
{
    protected static string $resource = ReferralSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
