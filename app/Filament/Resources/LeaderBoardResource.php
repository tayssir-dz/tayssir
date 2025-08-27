<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderBoardResource\Pages;
use App\Models\LeaderBoard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaderBoardResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('custom.nav.section.management');
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.leaderboard');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.leaderboard');
    }

    protected static ?string $model = LeaderBoard::class;

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('points', 'desc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('index')->label('')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('user.avatar_url')
                    ->toggleable()
                    ->label(__('custom.models.user.avatar'))
                    ->html()
                    ->width('60px')
                    ->alignment('center')
                    ->getStateUsing(fn($record) => view('components.filament-ui.avatar', [
                        'name' => $record->user->name,
                        'avatar_url' => $record->user->avatar_url,
                    ])->render()),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('custom.models.leaderboard.user'))
                    ->url(fn($record) => UserResource::getUrl('edit', ['record' => $record->user_id]))
                    ->searchable(),
                Tables\Columns\TextColumn::make('points')
                    ->badge()
                    ->color('primary')
                    ->label(__('custom.models.leaderboard.points'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('max')
                    ->badge()
                    ->color('primary')
                    ->label(__('custom.models.leaderboard.max'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('progress')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        $state < 20 => 'danger',
                        default => 'gray',
                    })
                    ->label(__('custom.models.leaderboard.progress'))
                    ->getStateUsing(fn($record) => number_format($record->user->progress_percentage, 1) . '%'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_user')
                    ->label(__('custom.models.user'))
                    ->url(fn($record) => UserResource::getUrl('edit', ['record' => $record->user_id]))
                    ->icon('heroicon-o-user'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaderBoards::route('/'),
            // 'create' => Pages\CreateLeaderBoard::route('/create'),
            // 'edit' => Pages\EditLeaderBoard::route('/{record}/edit'),
        ];
    }
}
