<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaderBoardResource\Pages;
use App\Filament\Resources\LeaderBoardResource\RelationManagers;
use App\Models\LeaderBoard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaderBoardResource extends Resource
{
    protected static ?string $model = LeaderBoard::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            ->columns([
                Tables\Columns\TextColumn::make('user.avatar_url')
                    ->toggleable()
                    ->html()
                    ->getStateUsing(fn($record) => view('components.filament-ui.avatar', [
                        'name' => $record->user->name,
                        'avatar_url' => $record->user->avatar_url,
                    ])->render()),
                Tables\Columns\TextColumn::make("points"),
                Tables\Columns\TextColumn::make("user.name"),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
