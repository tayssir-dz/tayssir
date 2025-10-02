<?php

namespace App\Filament\Admin\Resources\UserResource\RelationManagers;

use App\Filament\Admin\Resources\ChapterResource;
use App\Filament\Admin\Resources\MaterialResource;
use App\Filament\Admin\Resources\UnitResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public static function getModelLabel(): string
    {
        return __('custom.models.question.fill_blank.answer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.question.fill_blank.answers');
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.question.fill_blank.answers');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('material.name')
                    ->label(__('custom.models.material'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->url(fn($record) => MaterialResource::getUrl('edit', ['record' => $record->material_id], panel: 'dashboard'))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label(__('custom.models.unit'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->url(fn($record) => UnitResource::getUrl('edit', ['record' => $record->unit_id], panel: 'dashboard'))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('chapter.name')
                    ->label(__('custom.models.chapter'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->url(fn($record) => ChapterResource::getUrl('edit', ['record' => $record->chapter_id], panel: 'dashboard'))
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('question.question')
                    ->label(__('custom.models.question.question'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('points_earned')
                    ->label(__('custom.models.question.points'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('custom.table.created_at'))
                    ->getStateUsing(fn($record) => $record->created_at?->diffForHumans())
                    ->tooltip(fn($record) => $record->created_at?->format('Y-m-d H:i:s'))
                    ->sortable()
                    ->toggleable()
                    ->size('sm'),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
