<?php

namespace App\Filament\Admin\Resources\PromoterResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PromoCodesRelationManager extends RelationManager
{
    protected static string $relationship = 'promoCodes';

    public static function getModelLabel(): string
    {
        return __('custom.models.promoCode');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.promoCodes');
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('custom.models.promoCodes');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.promoCode.create.section.infos'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('custom.models.promoCode.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(2)
                            ->placeholder('PROMO2025')
                            ->helperText(__('custom.forms.promoCode.helper.code')),
                    ])
                    ->columns(2),

                Section::make(__('custom.forms.promoCode.create.section.dates'))
                    ->schema([
                        DatePicker::make('start_date')
                            ->label(__('custom.models.promoCode.start_date'))
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->closeOnDateSelection()
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label(__('custom.models.promoCode.end_date'))
                            ->required()
                            ->after('start_date')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make(__('custom.forms.promoCode.create.section.discounts'))
                    ->schema([
                        TextInput::make('student_discount')
                            ->label(__('custom.models.promoCode.student_discount'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->columnSpan(1)
                            ->helperText(__('custom.forms.promoCode.helper.student_discount')),

                        TextInput::make('promoter_margin')
                            ->label(__('custom.models.promoCode.promoter_margin'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->columnSpan(1)
                            ->helperText(__('custom.forms.promoCode.helper.promoter_margin')),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')
                    ->label(__('custom.models.promoCode.code'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable()
                    ->copyMessage('Code copied!')
                    ->copyMessageDuration(1500)
                    ->size('sm'),

                TextColumn::make('start_date')
                    ->label(__('custom.models.promoCode.start_date'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->size('sm'),

                TextColumn::make('end_date')
                    ->label(__('custom.models.promoCode.end_date'))
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->size('sm'),

                TextColumn::make('student_discount')
                    ->label(__('custom.models.promoCode.student_discount'))
                    ->suffix('%')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter()
                    ->size('sm')
                    ->color('success'),

                TextColumn::make('promoter_margin')
                    ->label(__('custom.models.promoCode.promoter_margin'))
                    ->suffix('%')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter()
                    ->size('sm')
                    ->color('info'),

                IconColumn::make('is_active')
                    ->label(__('custom.models.promoCode.is_active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->size('sm'),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active Only')
                    ->query(fn(Builder $query): Builder => $query->where(function ($query) {
                        $now = Carbon::now()->toDateString();

                        return $query->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                    }))
                    ->toggle(),

                Tables\Filters\Filter::make('expired')
                    ->label('Expired Only')
                    ->query(fn(Builder $query): Builder => $query->where('end_date', '<', Carbon::now()->toDateString()))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->modalWidth('3xl'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('3xl'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
