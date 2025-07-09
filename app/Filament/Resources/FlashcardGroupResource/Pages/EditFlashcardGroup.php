<?php

namespace App\Filament\Resources\FlashcardGroupResource\Pages;

use App\Filament\Resources\FlashcardGroupResource;
use App\Filament\Resources\MaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlashcardGroup extends EditRecord
{
    protected static string $resource = FlashcardGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];

        // Add Materials (plural)
        $breadcrumbs[MaterialResource::getUrl('index')] = __('custom.models.materials');

        // Add specific material name
        $breadcrumbs[MaterialResource::getUrl('edit', ['record' => $this->record->material_id])] = $this->record->material->name;

        // Add Flashcard Groups (plural)
        $breadcrumbs[''] = __('custom.models.flashcard_groups');

        // Add specific flashcard group name
        $breadcrumbs['#'] = $this->record->title;

        // Add Edit
        // $breadcrumbs[] = 'Edit';

        return $breadcrumbs;
    }
}
