<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\ContactFormResource\Pages;
use App\Models\ContactForm;
use Filament\Forms\Form;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static ?int $navigationSort = AdminNavigation::CONTACT_FORM_RESOURCE['sort'];
    protected static ?string $navigationIcon = AdminNavigation::CONTACT_FORM_RESOURCE['icon'];

    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::CONTACT_FORM_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.contact_form');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.contact_forms');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with('user'))
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('name')
                    ->label(__('custom.models.contact_form.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label(__('custom.models.contact_form.email'))
                    ->copyable()
                    ->copyMessage('Copied')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.phone_number')
                    ->label(__('custom.models.contact_form.phone_number'))
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                TextColumn::make('subject')
                    ->label(__('custom.models.contact_form.subject'))
                    ->limit(40)
                    ->placeholder(__('custom.models.generic.empty'))
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('message')
                    ->label(__('custom.models.contact_form.message'))
                    ->limit(length: 25)
                    ->grow()
                    ->placeholder(__('custom.models.generic.empty'))
                    // ->wrap()
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label(__('custom.models.contact_form.user'))
                    ->placeholder(__('custom.models.generic.empty'))
                    ->toggleable(),
                IconColumn::make('is_solved')
                    ->label(__('custom.models.contact_form.is_solved'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('custom.table.created_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                // Tables\Filters\TernaryFilter::make('is_solved')
                //     ->label(__('is solved')),
                // keep simple for now
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\Action::make('reply')
                //         ->label(__('custom.models.contact_form.actions.reply'))
                //         ->color('primary')
                //         ->url(fn($record) => 'mailto:' . $record->email . '?subject=' . rawurlencode($record->subject))
                //         ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make()
                    ->label(__('custom.models.generic.delete')),
                // ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('custom.models.generic.delete_selected')),
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
            'index' => Pages\ListContactForms::route('/'),
            'view' => Pages\ViewContactForm::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make(__('custom.models.contact_form.sections.contact_info'))
                    ->schema([
                        TextEntry::make('user.name'),
                        TextEntry::make('name')->label(__('custom.models.contact_form.name')),
                        TextEntry::make('email')->label(__('custom.models.contact_form.email')),
                        TextEntry::make('user.phone_number')->label(__('custom.models.contact_form.phone_number')),
                        TextEntry::make('subject')
                            ->label(__('custom.models.contact_form.subject'))
                            ->placeholder(__('custom.models.generic.empty')),
                        TextEntry::make('is_solved')
                            ->label(__('custom.models.contact_form.is_solved'))
                            ->formatStateUsing(fn($state) => $state ? __('Yes') : __('No')),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label(__('custom.table.created_at')),
                    ]),
                InfoSection::make(__('custom.models.contact_form.sections.content'))
                    ->schema([
                        TextEntry::make('message')
                            ->label(__('custom.models.contact_form.message'))
                            ->placeholder(__('custom.models.generic.empty'))
                            ->columnSpanFull()
                            ->markdown(),
                    ]),
                // InfoSection::make(__('custom.models.contact_form.sections.metadata'))
                //     ->collapsible()
                //     ->collapsed()
                //     ->schema([
                //         KeyValueEntry::make('metadata')
                //             ->label(__('custom.models.contact_form.metadata'))
                //             ->placeholder(__('custom.models.generic.empty'))
                //             ->columnSpanFull(),
                //     ]),
            ]);
    }
}
