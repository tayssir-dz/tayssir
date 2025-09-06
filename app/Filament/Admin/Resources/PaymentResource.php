<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Purchase\PaymentStatus;
use App\Enums\Purchase\PaymentType;
use App\Filament\Admin\AdminNavigation;
use App\Filament\Admin\Resources\PaymentResource\Pages;
use App\Filament\Admin\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = AdminNavigation::PAYMENT_RESOURCE['icon'];


    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::SUBSCRIPTION_AND_PAYMENT_GROUP);
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.payments');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?int $navigationSort = AdminNavigation::PAYMENT_RESOURCE['sort'];

    // protected static ?string $recordTitleAttribute = 'id';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Status')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options(PaymentStatus::class),
                    ]),
                Section::make('Payment Info')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_type')
                            ->options(PaymentType::class)
                            ->disabled(true),
                        TextInput::make('price')->disabled(true),
                        TextInput::make('final_price')->disabled(true),
                    ]),
                Section::make('Discounts')
                    ->columns(2)
                    ->schema([
                        TextInput::make('discount_percentage')->disabled(true),
                        TextInput::make('discount_amount')->disabled(true),
                        TextInput::make('promocode_percentage')->disabled(true),
                        TextInput::make('promocode_amount')->disabled(true),
                        TextInput::make('combined_discount_percentage')->disabled(true),
                        TextInput::make('combined_discount_amount')->disabled(true),
                    ]),
                Section::make('Promoter Margin')
                    ->columns(2)
                    ->schema([
                        TextInput::make('promoter_margin_percentage')->disabled(true),
                        TextInput::make('promoter_margin_amount')->disabled(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->defaultGroup('payment_type')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('user.email')
                    ->label(__('custom.models.user.email'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('subscription.name')
                    ->label(__('custom.models.subscription'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('custom.models.payment.status'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('payment_type')
                    ->label(__('custom.models.payment.type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('price')
                    ->label(__('custom.models.payment.price'))
                    //->numeric(2)
                    ->sortable()
                    ->suffix(' DZD')
                    ->toggleable(),
                TextColumn::make('discount_percentage')
                    ->label(__('custom.models.payment.discount_percentage'))
                    //->numeric(2)
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discount_amount')
                    ->label(__('custom.models.payment.discount_amount'))
                    //->numeric(2)
                    ->suffix(' DZD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('promocode_percentage')
                    ->label(__('custom.models.payment.promocode_percentage'))
                    //->numeric(2)
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('promocode_amount')
                    ->label(__('custom.models.payment.promocode_amount'))
                    //->numeric(2)
                    ->suffix(' DZD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('combined_discount_percentage')
                    ->label(__('custom.models.payment.combined_discount_percentage'))
                    //->numeric(2)
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('combined_discount_amount')
                    ->label(__('custom.models.payment.combined_discount_amount'))
                    //->numeric(2)
                    ->suffix(' DZD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('final_price')
                    ->label(__('custom.models.payment.final_price'))
                    //->numeric(2)
                    ->sortable()
                    ->suffix(' DZD')
                    ->toggleable(),
                TextColumn::make('promoter_margin_percentage')
                    ->label(__('custom.models.payment.promoter_margin_percentage'))
                    //->numeric(2)
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('promoter_margin_amount')
                    ->label(__('custom.models.payment.promoter_margin_amount'))
                    //->numeric(2)
                    ->suffix(' DZD')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('custom.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('custom.models.payment.status'))
                    ->options(\App\Enums\Purchase\PaymentStatus::class),
                SelectFilter::make('payment_type')
                    ->label(__('custom.models.payment.type'))
                    ->options(\App\Enums\Purchase\PaymentType::class),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
