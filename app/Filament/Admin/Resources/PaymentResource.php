<?php

namespace App\Filament\Admin\Resources;

use App\Enums\Purchase\PaymentStatus;
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
                Section::make()->columns(3)->schema([
                    Select::make("status")->options(PaymentStatus::class),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make("id"),
                TextColumn::make("user.email"),
                TextColumn::make("subscription.name"),
                TextColumn::make("status"),
                TextColumn::make("price")->label("Original price"),
                TextColumn::make("final_price"),
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
