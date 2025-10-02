<?php

namespace App\Filament\Admin\Resources\ContactFormResource\Pages;

use App\Filament\Admin\Resources\ContactFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactForm extends ViewRecord
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ActionGroup::make([
            // Actions\Action::make('reply')
            //     ->label(__('custom.models.contact_form.actions.reply'))
            //     ->color('primary')
            //     ->url(fn($record) => 'mailto:' . $record->email . '?subject=' . rawurlencode($record->subject))
            //     ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->label(__('custom.models.generic.delete')),
            // ]),
        ];
    }
}
