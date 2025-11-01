<?php

namespace App\Filament\Admin\Resources\ContactFormResource\Pages;

use App\Filament\Admin\Resources\ContactFormResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListContactForms extends ListRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('custom.models.contact_form.tabs.all'))
                ->icon('heroicon-o-circle-stack'),
            'solved' => Tab::make(__('custom.models.contact_form.tabs.solved'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_solved', true)),
            'unsolved' => Tab::make(__('custom.models.contact_form.tabs.unsolved'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_solved', false)),
        ];
    }
}
