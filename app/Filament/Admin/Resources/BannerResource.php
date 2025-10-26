<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::BANNER_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.banner');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.banners');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationIcon = AdminNavigation::BANNER_RESOURCE['icon'];

    protected static ?int $navigationSort = AdminNavigation::BANNER_RESOURCE['sort'];

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.forms.banner.create.section.infos'))->schema([
                    TextInput::make('title')
                        ->minLength(3)
                        ->label(__('custom.models.banner.title'))
                        ->columnSpan(2),

                    Textarea::make('description')
                        ->rows(3)
                        ->label(__('custom.models.banner.description'))
                        ->columnSpan(2),

                    TextInput::make('action_url')
                        ->url()
                        ->label(__('custom.models.banner.action_url')),

                    TextInput::make('action_label')
                        ->label(__('custom.models.banner.action_label')),

                    Toggle::make('is_active')
                        ->label(__('custom.models.banner.is_active'))
                        ->default(true),
                ])->columnSpan(3)->columns(2),

                Section::make(__('custom.forms.banner.create.section.styling'))->schema([
                    ColorPicker::make('gradient_start')
                        ->required()
                        ->label(__('custom.models.banner.gradient_start')),

                    ColorPicker::make('gradient_end')
                        ->required()
                        ->label(__('custom.models.banner.gradient_end')),
                ])->columnSpan(2),

                Section::make(__('custom.forms.banner.create.section.image'))->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->multiple(false)
                        ->label('')
                        ->collection('image')
                        ->image()
                        ->imageEditor(),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('custom.models.banner.image'))
                    ->placeholder(__('custom.table.image.empty'))
                    ->collection('image')
                    ->conversion('thumb')
                    ->circular(),

                TextColumn::make('title')
                    ->placeholder('-')
                    ->label(__('custom.models.banner.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->limit(50)
                    ->label(__('custom.models.banner.description')),

                TextColumn::make('action_label')
                    ->label(__('custom.models.banner.action_label'))
                    ->badge()
                    ->color('primary'),

                ColorColumn::make('gradient_start')
                    ->label(__('custom.models.banner.gradient_start'))
                    ->toggleable(),

                ColorColumn::make('gradient_end')
                    ->label(__('custom.models.banner.gradient_end'))
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label(__('custom.models.banner.is_active'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('custom.models.banner.is_active'))
                    ->boolean()
                    ->trueLabel(__('custom.models.active.true'))
                    ->falseLabel(__('custom.models.active.false'))
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
