<?php

namespace App\Filament\Admin\Resources\QuestionReportResource\Pages;

use App\Filament\Admin\Resources\QuestionReportResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListQuestionReports extends ListRecords
{
    protected static string $resource = QuestionReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('custom.models.question_report.tabs.all'))
                ->icon('heroicon-o-circle-stack'),
            'solved' => Tab::make(__('custom.models.question_report.tabs.solved'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_solved', true)),
            'unsolved' => Tab::make(__('custom.models.question_report.tabs.unsolved'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_solved', false)),
            'contacted' => Tab::make(__('custom.models.question_report.tabs.contacted'))
                ->icon('heroicon-o-phone')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_contacted', true)),
            'uncontacted' => Tab::make(__('custom.models.question_report.tabs.uncontacted'))
                ->icon('heroicon-o-phone-x-mark')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_contacted', false)),
        ];
    }
}
