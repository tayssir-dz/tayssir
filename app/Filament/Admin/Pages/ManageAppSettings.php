<?php

namespace App\Filament\Admin\Pages;

use App\Settings\AppSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Lang;

class ManageAppSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = null;

    protected static ?int $navigationSort = 3;

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
        return  __('custom.nav.section.app');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(Lang::get('custom.settings.app.section.information'))
                    ->schema([
                        TextInput::make('app_version')
                            ->label(Lang::get('custom.settings.app.version')),
                        Toggle::make('resumes_active')
                            ->label(Lang::get('custom.settings.app.resumes')),
                        Toggle::make('bac_solutions_active')
                            ->label(Lang::get('custom.settings.app.bac_solutions')),
                        Toggle::make('cards_tools_active')
                            ->label(Lang::get('custom.settings.app.cards_tools')),
                    ]),
            ]);
    }
}
