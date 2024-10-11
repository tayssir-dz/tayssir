<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Date;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Support\Utils;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $recordTitleAttribute = 'email';
    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('custom.nav.section.management')
            : '';
    }

    public static function getModelLabel(): string
    {
        return __('custom.models.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('custom.models.users');
    }
    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->count())
            : null;
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'assign_role',
            'verify_email',
            'view_all',
            'view_students',
            'view_with_roles',
        ];
    }
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 3;

    protected static bool $isGloballySearchable = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('custom.models.user.perfonal_info'))
                    ->schema([
                        TextInput::make("email")->required()->email()->label(__('custom.models.user.email')),
                        TextInput::make("name")->required()->label(__('custom.models.user.name')),
                        PhoneInput::make('phone_number')->label(__('custom.models.user.phone')),
                        TextInput::make("password")->password()->required()->label(__('custom.models.user.password'))->visibleOn('create'),
                        TextInput::make("points")->numeric()->required()->label(__('custom.models.user.points'))->visibleOn('edit')

                    ])->columnSpan(2)->columns(1),
                Group::make()->schema([
                    Section::make(__('custom.models.user.avatar'))->schema([
                        FileUpload::make("avatar_url")->image()->imageEditor()
                            ->directory('avatars')
                            ->deletable()
                            ->label("")
                    ]),
                    Section::make(__('custom.models.user.roles_and_subscription'))->schema([

                        Select::make('role')
                            ->label(__('custom.models.user.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(auth()->user()->can("assign_role_user"))
                            ->label(__('role')),
                    ]),
                ])

            ])->columns(3);
    }


    public static function table(Table $table): Table
    {
        return $table
            // ->paginated(false)
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label(__('custom.models.user.avatar'))
                    ->default("https://static.vecteezy.com/system/resources/previews/020/911/746/non_2x/user-profile-icon-profile-avatar-user-icon-male-icon-face-icon-profile-icon-free-png.png")
                    ->alignCenter()
                    ->circular(),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(__('custom.models.user.name')),

                TextColumn::make('email')
                    // ->weight(FontWeight::SemiBold)
                    // ->color("gray")
                    ->sortable()
                    ->searchable()
                    ->label(__('custom.models.user.email')),

                PhoneColumn::make('phone_number')
                    ->weight(FontWeight::SemiBold)
                    ->label(__('custom.models.user.phone')),

                TextColumn::make('points')
                    ->badge()
                    ->color("success")
                    ->sortable()
                    ->alignCenter()
                    ->label(__('custom.models.user.points'))
                    ->numeric(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->alignCenter()
                    ->color("primary")
                    ->sortable()
                    ->searchable()
                    ->label(__('custom.models.user.roles')),

                ToggleColumn::make('email_verified_at')
                    ->sortable()
                    // ->alignCenter()
                    ->label(__('custom.models.user.email_verified'))
                    // ->visible(auth()->user()->can("verify_email_user"))
                    ->afterStateUpdated(function ($state, $record) {
                        if ($state) {
                            $record->email_verified_at = Date::now();
                            $record->save();
                        } else {
                            $record->email_verified_at = null;
                            $record->save();
                        }
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')->relationship("roles", "name")->multiple()->preload()
                    ->searchable()->label(__('custom.models.user.roles')),

                // Tables\Filters\Filter::make('email_verified')
                //     ->query(fn(Builder $query): Builder => $query->where('email_verified_at', '!=', null))
                //     ->toggle()->label(__('custom.models.user.email_verified')),

                // Tables\Filters\TernaryFilter::make('email_verified')
                //     ->placeholder('All')
                //     ->trueLabel('Verified')
                //     ->falseLabel('Not Verified')
                //     ->queries(
                //         true: fn(Builder $query) => $query->where('email_verified_at', '!=', null),
                //         false: fn(Builder $query) => $query->where('email_verified_at', '=', null),
                //         blank: fn(Builder $query) => $query,
                //     )

                // Tables\Filters\Filter::make('email_verified')
                //     ->query(fn(Builder $query): Builder => $query->where('email_verified_at', '!=', null))
                //     ->label(__('custom.models.user.email_verified')),

                Tables\Filters\Filter::make('email_verified')
                    ->query(fn(Builder $query): Builder => $query->where('email_verified_at', '!=', null))
                    ->label(__('custom.models.user.email_verified')),
                // ->visible(auth()->user()->can("verify_email_user")),

                Tables\Filters\Filter::make('email_not_verified')
                    ->query(fn(Builder $query): Builder => $query->where('email_verified_at', '=', null))
                    ->label(__('custom.models.user.email_not_verified')),
                // ->visible(auth()->user()->can("verify_email_user")),

                // Tables\Filters\Filter::make('email_verified_at')
                //     ->query(fn(Builder $query): Builder => $query->where('email_verified_at', '==', null))
                //     ->toggle()->label(__('custom.models.user.email_not_verified')),

                // Tables\Filters\SelectFilter::make('email_verified')->options([
                //     '*' => 'Verified',
                //     null => 'Not Verified',
                // ])->label(__('custom.models.user.email_verified'))->multiple(),





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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
