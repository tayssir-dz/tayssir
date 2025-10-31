<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\ReferralSourceResource\Pages;
use App\Models\ReferralSource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReferralSourceResource extends Resource
{
    protected static ?string $model = ReferralSource::class;

    protected static ?string $navigationIcon = AdminNavigation::REFERRAL_SOURCE_RESOURCE['icon'];

    protected static ?int $navigationSort = AdminNavigation::REFERRAL_SOURCE_RESOURCE['sort'];

    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::REFERRAL_SOURCE_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.referral_source');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.referral_sources');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.models.referral_sources'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('custom.models.referral_source.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull()
                            ->minLength(2)
                            ->maxLength(50),

                        SpatieMediaLibraryFileUpload::make('icon')
                            ->label(__('custom.models.referral_source.icon'))
                            ->collection('icon')
                            ->conversion('thumb')
                            ->getUploadedFileNameForStorageUsing(fn($file) => str()->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.svg')
                            ->acceptedFileTypes(['image/svg+xml'])
                            ->rules(['mimetypes:image/svg+xml'])
                            ->imageEditor(false)
                            ->image()
                            ->preserveFilenames()
                            ->multiple(false)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('icon')
                    ->collection('icon')
                    ->label(__('custom.models.referral_source.icon'))
                    ->height(40)
                    ->width(40)
                    ->circular()
                    ->extraImgAttributes(['class' => 'bg-white p-1 rounded'])
                    ->placeholder(__('custom.table.image.empty')),
                TextColumn::make('name')
                    ->label(__('custom.models.referral_source.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('users_count')
                    ->label(__('custom.models.referral_source.users_count'))
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->label(__('custom.table.created_at'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageReferralSources::route('/'),
        ];
    }
}
