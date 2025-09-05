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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = AdminNavigation::PAYMENT_RESOURCE['icon'];


    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::PAYMENT_RESOURCE['group']);
    }

    public static function getModelLabel(): string
    {

        // return __('custom.models.subscription');
        return "payment";
    }

    public static function getPluralModelLabel(): string
    {
        // return __('custom.models.subscriptions');
        return "payments";
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
                TextColumn::make("id")->toggleable(),
                TextColumn::make("user.email")->toggleable(),
                TextColumn::make("subscription.name")->toggleable(),
                TextColumn::make("status")->badge()->toggleable(),
                TextColumn::make("payment_type")->badge()->toggleable(),
                TextColumn::make('price')->toggleable(),
                TextColumn::make('discount_percentage')->toggleable(),
                TextColumn::make('discount_amount')->toggleable(),
                TextColumn::make('promocode_percentage')->toggleable(),
                TextColumn::make('promocode_amount')->toggleable(),
                TextColumn::make('combined_discount_percentage')->toggleable(),
                TextColumn::make('combined_discount_amount')->toggleable(),
                TextColumn::make('final_price')->toggleable(),
                TextColumn::make('promoter_margin_percentage')->toggleable(),
                TextColumn::make('promoter_margin_amount'),
            ])
            ->defaultSort('created_at', 'desc')
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
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
