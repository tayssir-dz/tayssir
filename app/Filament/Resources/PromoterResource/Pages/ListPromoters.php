<?php

namespace App\Filament\Resources\PromoterResource\Pages;

use App\Filament\Resources\PromoterResource;
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
