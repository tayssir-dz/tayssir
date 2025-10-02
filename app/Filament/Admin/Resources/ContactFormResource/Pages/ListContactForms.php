<?php

namespace App\Filament\Admin\Resources\ContactFormResource\Pages;

use App\Filament\Admin\Resources\ContactFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactForms extends ListRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
