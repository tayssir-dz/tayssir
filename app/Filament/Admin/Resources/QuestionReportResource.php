<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\QuestionReportResource\Pages;
use App\Filament\Admin\Resources\QuestionReportResource\RelationManagers;
use App\Models\QuestionReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionReportResource extends Resource
{
    protected static ?string $model = QuestionReport::class;
    protected static ?int $navigationSort = AdminNavigation::QUESTION_REPORT_RESOURCE['sort'];
    protected static ?string $navigationIcon = AdminNavigation::QUESTION_REPORT_RESOURCE['icon'];
    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::QUESTION_REPORT_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('question report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('question reports');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListQuestionReports::route('/'),
            'create' => Pages\CreateQuestionReport::route('/create'),
            'edit' => Pages\EditQuestionReport::route('/{record}/edit'),
        ];
    }
}
