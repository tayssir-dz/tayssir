<?php

namespace App\Providers\Filament;

use App\Filament\Resources\UserResource\Widgets\UsersCard;
use App\View\Components\Logo;
use DutchCodingCompany\FilamentDeveloperLogins\FilamentDeveloperLoginsPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Howdu\FilamentRecordSwitcher\FilamentRecordSwitcherPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Illuminate\Support\Facades\Blade;

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
            ->colors(
                [
                    'primary' => Color::hex("#00C4F6"),
                    // 'primary' => Color::Green,
                    'success' => Color::hex("#12D18E"),
                    'error' => Color::hex("#F85556"),
                    'warning' => Color::hex("#FF9500"),
                    'info' => Color::hex("#5ACD76"),
                    'neutral' => Color::hex("#E5E7EB"),
                ]
            )->font("Poppins")
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\FilamentInfoWidget::class,
                // Widgets\AccountWidget::class,
                UsersCard::class,
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
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->gridColumns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns(['default' => 1])
                    ->resourceCheckboxListColumns(['default' => 1, 'sm' => 2]),
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
                // FilamentDeveloperLoginsPlugin::make()
                //     ->enabled(true)
                //     ->users([
                //         'dev login' => 'admin@admin.dev',
                //         // 'mouayed' => 'm_keziz@estin.dz',
                //         // 'something else' => "somethingelse@something.com"
                //     ]),
                FilamentRecordSwitcherPlugin::make(),

            ])
            // ->spa()
            // ->darkMode(false)
            ->renderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"))
            ->viteTheme('resources/css/filament/dashboard/theme.css');;
    }
}
