<?php

namespace App\Filament\Admin\Resources\PromoterResource\Pages;

use App\Filament\Admin\Resources\PromoterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromoter extends EditRecord
{
    protected static string $resource = PromoterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
