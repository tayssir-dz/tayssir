<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\AdminNavigation;
use App\Settings\AppSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Lang;

class ManageAppSettings extends SettingsPage
{
    protected static ?string $navigationIcon = AdminNavigation::APP_SETTINGS['icon'];

    protected static ?string $title = null;

    protected static ?int $navigationSort = AdminNavigation::APP_SETTINGS['sort'];

    protected static string $settings = AppSettings::class;

    public static function getNavigationLabel(): string
    {
        return Lang::get('custom.settings.app.title');
    }

    public function getTitle(): string
    {
        return Lang::get('custom.settings.app.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::APP_SETTINGS['group']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('settings_tabs')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(Lang::get('custom.settings.app.section.information'))
                            ->schema([
                                TextInput::make('app_version')
                                    ->required()
                                    ->label(Lang::get('custom.settings.app.version')),
                                Toggle::make('resumes_active')
                                    ->required()
                                    ->label(Lang::get('custom.settings.app.resumes')),
                                Toggle::make('bac_solutions_active')
                                    ->required()
                                    ->label(Lang::get('custom.settings.app.bac_solutions')),
                                Toggle::make('cards_tools_active')
                                    ->required()
                                    ->label(Lang::get('custom.settings.app.cards_tools')),
                            ]),
                        Tab::make(Lang::get('custom.settings.payment.section.information'))
                            ->schema([

                                TextInput::make('payment_name')
                                    ->required()
                                    ->label(Lang::get('custom.settings.payment.name')),
                                TextInput::make('payment_number')
                                    ->required()
                                    ->rules(['numeric', 'digits:20'])
                                    ->label(Lang::get('custom.settings.payment.number')),
                                Toggle::make('payment_active')
                                    ->required()
                                    ->label(Lang::get('custom.settings.payment.active')),
                                Toggle::make('chargily_payment_active')
                                    ->required()
                    ->label(Lang::get('custom.settings.payment.chargily_active')),
                            ]),
                    ]),
            ]);
    }
}
