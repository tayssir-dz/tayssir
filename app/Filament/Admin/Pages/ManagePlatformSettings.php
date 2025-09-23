<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\AdminNavigation;
use App\Settings\PlatformSettings;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Lang;

class ManagePlatformSettings extends SettingsPage
{
    protected static ?string $navigationIcon = AdminNavigation::PLATFORM_SETTINGS['icon'];

    protected static ?string $title = null;

    protected static ?int $navigationSort = AdminNavigation::PLATFORM_SETTINGS['sort'];

    protected static string $settings = PlatformSettings::class;

    public static function getNavigationLabel(): string
    {
        return __('custom.settings.platform.title');
    }

    public function getTitle(): string
    {
        return __('custom.settings.platform.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return  __(AdminNavigation::PLATFORM_SETTINGS['group']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Platform Settings')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make(__('custom.settings.platform.section.social_media'))
                            ->schema([
                                // Instagram
                                TextInput::make('instagram_url')
                                    ->columnSpan(3)
                                    ->label(__('custom.settings.platform.instagram.url'))
                                    ->url()
                                    ->placeholder('https://instagram.com/username'),
                                Toggle::make('instagram_active')
                                    ->inline(false)
                                    ->label(__('custom.settings.platform.instagram.active')),

                                // Facebook
                                TextInput::make('facebook_url')
                                    ->columnSpan(3)
                                    ->label(__('custom.settings.platform.facebook.url'))
                                    ->url()
                                    ->placeholder('https://facebook.com/pagename'),
                                Toggle::make('facebook_active')
                                    ->inline(false)
                                    ->label(__('custom.settings.platform.facebook.active')),

                                // TikTok
                                TextInput::make('tiktok_url')
                                    ->columnSpan(3)
                                    ->label(__('custom.settings.platform.tiktok.url'))
                                    ->url()
                                    ->placeholder('https://tiktok.com/@username'),
                                Toggle::make('tiktok_active')
                                    ->inline(false)
                                    ->label(__('custom.settings.platform.tiktok.active')),

                                // YouTube
                                TextInput::make('youtube_url')
                                    ->columnSpan(3)
                                    ->label(__('custom.settings.platform.youtube.url'))
                                    ->url()
                                    ->placeholder('https://youtube.com/@channelname'),
                                Toggle::make('youtube_active')
                                    ->inline(false)
                                    ->label(__('custom.settings.platform.youtube.active')),

                                // LinkedIn
                                TextInput::make('linkedin_url')
                                    ->columnSpan(3)
                                    ->label(__('custom.settings.platform.linkedin.url'))
                                    ->url()
                                    ->placeholder('https://linkedin.com/company/companyname'),
                                Toggle::make('linkedin_active')
                                    ->inline(false)
                                    ->label(__('custom.settings.platform.linkedin.active')),
                            ])
                            ->columns(4),
                        Tabs\Tab::make(__('custom.settings.platform.section.users_scope'))
                            ->schema([
                                DatePicker::make('platform_active_from')
                                    ->label(__('custom.settings.platform.users_scope.active_from'))
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->placeholder('YYYY-MM-DD'),
                                DatePicker::make('platform_active_to')
                                    ->label(__('custom.settings.platform.users_scope.active_to'))
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->placeholder('YYYY-MM-DD'),
                            ])
                            ->columns(2),

                    ]),
            ]);
    }
}
