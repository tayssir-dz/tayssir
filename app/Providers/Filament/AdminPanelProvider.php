<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use DutchCodingCompany\FilamentDeveloperLogins\FilamentDeveloperLoginsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Howdu\FilamentRecordSwitcher\FilamentRecordSwitcherPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->databaseTransactions()
            ->brandLogo(fn() => view('components.brand'))
            ->darkModeBrandLogo(fn() => view('components.brand-dark'))
            ->brandLogoHeight('2rem')
            ->favicon(asset(('favicon.svg')))
            ->colors(
                [
                    'primary' => Color::hex('#00C4F6'),
                    'success' => Color::hex('#12D18E'),
                    'error' => Color::hex('#F85556'),
                    'warning' => Color::hex('#FF9500'),
                    'info' => Color::hex('#F037A5'),
                    'neutral' => Color::hex('#E5E7EB'),
                ]
            )
            ->databaseNotifications()
            ->databaseNotificationsPolling("30s")
            ->lazyLoadedDatabaseNotifications(false)
            ->font('Poppins')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                // Widgets\FilamentInfoWidget::class,
                // Widgets\AccountWidget::class,
                // Widgets\ReferralSourcesBarChart::class,
                // Widgets\GeneralInfos::class,
                // Widgets\PlatformAnalytics::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->sidebarCollapsibleOnDesktop()
            // ->sidebarFullyCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ])->plugins([
                GlobalSearchModalPlugin::make()
                    ->slideOver(),
                FilamentEditProfilePlugin::make()
                    ->shouldShowEditProfileForm(true)
                    // ->canAccess(fn() => auth()->user()->can('page_EditProfilePage'))
                    // ->shouldShowSanctumTokens(true)
                    ->setIcon('heroicon-o-user')
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
                        rules: 'mimes:jpeg,png|max:1024'
                    ),
                FilamentDeveloperLoginsPlugin::make()
                    ->enabled(config('app.debug', false))
                    ->users([
                        'ADMIN' => 'admin@tayssir-bac.com',
                    ]),
                FilamentRecordSwitcherPlugin::make(),

            ])
            ->spa()
            // ->darkMode(false)
            ->renderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"))
            ->viteTheme('resources/css/filament/dashboard/theme.css');
    }
}
