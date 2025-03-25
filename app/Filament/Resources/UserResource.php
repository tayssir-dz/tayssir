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
use Filament\Forms\Components\TagsInput;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Support\Utils;
use Kossa\AlgerianCities\Commune;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Filament\Tables\Table;  // Add this line
use Filament\Forms\Components\CheckboxList; // Add this to use statements
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

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
            'assign_division',
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
                        TextInput::make("name")
                            ->required()
                            ->label(__('custom.models.user.name'))
                            ->columnSpan(2),

                        TextInput::make("email")
                            ->disabledOn("edit")
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->email()
                            ->label(__('custom.models.user.email')),

                        PhoneInput::make('phone_number')
                            ->disabledOn("edit")
                            ->label(__('custom.models.user.phone')),

                        TextInput::make("password")
                            ->password()
                            ->required()
                            ->label(__('custom.models.user.password'))
                            ->visibleOn('create'),

                        // TextInput::make("points")
                        //     ->numeric()
                        //     ->required()
                        //     ->label(__('custom.models.user.points'))
                        //     ->visibleOn('edit')->columnSpan(2),

                        Select::make('wilaya_id')
                            ->label(__("custom.models.user.wilaya"))
                            ->relationship(name: 'wilaya', titleAttribute: __("custom.models.user.wilaya.field"))  // Select field for wilaya
                            ->searchable()
                            ->preload()
                            ->reactive()  // Makes it reactive to changes
                            ->afterStateUpdated(fn(callable $set) => $set('commune_id', null)),  // Clear commune when wilaya changes

                        Select::make('commune_id')
                            ->label(__("custom.models.user.commune"))
                            ->options(function (callable $get) {
                                $wilayaId = $get('wilaya_id');
                                $field = __("custom.models.user.wilaya.field"); // 'name' or 'arabic_name' based on the language

                                if ($wilayaId) {
                                    // Query the communes based on the selected wilaya and the dynamic field
                                    $communes = Commune::where('wilaya_id', $wilayaId)
                                        ->pluck($field, 'id')  // Return array of id => name or arabic_name
                                        ->toArray();

                                    return $communes;
                                }

                                return [];
                            })
                            ->disabled(fn(callable $get) => !$get('wilaya_id'))  // Disable if no Wilaya selected
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                if (!$get('wilaya_id')) {
                                    $set('commune_id', null);
                                }
                            }),
                        Select::make('role')
                            ->label(__('custom.models.user.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->visible(auth()->user()->can("assign_role_user"))
                            ->label(__('role'))
                            ->columnSpan(2),

                        Select::make('division')
                            ->label(__('custom.models.user.division'))
                            ->relationship('division', 'name')
                            ->preload()
                            ->searchable()
                            ->visible(auth()->user()->can("assign_division_user"))
                            ->label(__('division'))
                            ->columnSpan(2),

                        Placeholder::make('active_subscriptions')
                            ->label(__('custom.models.user.subscribtion'))
                            ->content(function ($record) {
                                if (!$record)
                                    return '';

                                return new HtmlString(
                                    $record->active_subscriptions
                                        ->map(fn($sub) => sprintf(
                                            '<div class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-primary-500 text-white">%s</div>',
                                            $sub->name
                                        ))
                                        ->join(' ')
                                );
                            })
                            ->columnSpan(2),

                    ])->columnSpan(2)->columns(2),
                Group::make()->schema([
                    Section::make(__('custom.models.user.avatar'))->schema([
                        FileUpload::make("avatar_url")->image()->imageEditor()
                            ->directory('avatars')
                            ->deletable()
                            ->label("")
                    ]),
                ])

            ])->columns(3);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('avatar_url')
                    ->toggleable()
                    ->label(__('custom.models.user.avatar'))
                    ->html()
                    ->getStateUsing(fn($record) => view('components.filament-ui.avatar', [
                        'name' => $record->name,
                        'avatar_url' => $record->avatar_url,
                    ])->render()),

                TextColumn::make('name')
                    ->label(__('custom.models.user.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->weight(FontWeight::Bold)
                    ->size('sm')
                    ->description(fn($record) => view('components.small-text')->with([
                        'text' => $record->email
                    ])),

                PhoneColumn::make('phone_number')
                    ->label(__('custom.models.user.phone'))
                    ->default(__("custom.models.user.phone.empty"))
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->size('sm')
                    ->copyMessage('Phone number copied')
                    ->copyMessageDuration(1500),

                // TextColumn::make('points')
                //     ->label(__('custom.models.user.points'))
                //     ->badge()
                //     ->color(fn($state) => $state > 1000 ? 'success' : 'warning')
                //     ->alignCenter()
                //     ->sortable()
                //     ->toggleable()
                //     ->size('sm')
                //     ->numeric(),

                TextColumn::make('roles.name')
                    ->label(__('custom.models.user.roles'))
                    ->badge()
                    ->toggleable()
                    ->color('primary')
                    ->separator(', ')
                    ->size('sm')
                    ->default(__('custom.models.user.roles.empty'))
                    ->wrap(),

                TextColumn::make('subscriptions')
                    ->label(__('custom.models.user.subscribtion'))
                    ->badge()
                    ->wrap()
                    ->toggleable()
                    ->color('success')
                    ->size('sm')
                    ->getStateUsing(function ($record) {
                        return $record->active_subscriptions
                            ->map(fn($sub) => $sub->name)
                            ->values()
                            ->toArray() ?: ['-'];
                    })
                    ->wrap(),

                TextColumn::make('wilaya.' . __('custom.models.user.wilaya.field'))
                    ->label(__('custom.models.user.wilaya'))
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->size('sm')
                    ->default(__('custom.models.user.wilaya.empty'))
                    ->description(fn($record) => $record->commune?->{__('custom.models.user.commune.field')} ?? __('custom.models.user.commune.empty')),

                ToggleColumn::make('email_verified_at')
                    ->label(__('custom.models.user.verified'))
                    ->sortable()
                    ->toggleable()
                    ->alignCenter()
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label(__('custom.models.user.roles')),

                Tables\Filters\SelectFilter::make('wilaya')
                    ->relationship('wilaya', __('custom.models.user.wilaya.field'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label(__('custom.models.user.wilayas')),

                Tables\Filters\TernaryFilter::make('email_verified')
                    ->label(__('custom.models.user.email_verified'))
                    ->placeholder(__('custom.models.user.tabs.all'))
                    ->trueLabel(__('custom.models.user.email_verified'))
                    ->falseLabel(__('custom.models.user.email_not_verified'))
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn(Builder $query) => $query->whereNull('email_verified_at'),
                    ),
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
