<?php

namespace App\Filament\Admin\Resources\PromoterResource\Pages;

use App\Filament\Admin\Resources\PromoterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromoters extends ListRecords
{
    protected static string $resource = PromoterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
