<?php

namespace App\Providers\Filament;

use App\Filament\Promoter\Widgets\MostUsedPromoCode;
use App\Filament\Promoter\Widgets\PromoterStats;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Pages;
use Filament\Widgets;

class PromoterPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('promoter')
            ->path('promoter')
            ->login()
            ->authGuard('promoter')
            ->colors(
                [
                    'primary' => Color::hex('#F037A5'),
                    'success' => Color::hex('#12D18E'),
                    'error' => Color::hex('#F85556'),
                    'warning' => Color::hex('#FF9500'),
                    'info' => Color::hex('#00C4F6'),
                    'neutral' => Color::hex('#E5E7EB'),
                ]
            )
            ->spa()
            ->databaseNotifications()
            ->font('Poppins')
            ->discoverPages(in: app_path('Filament/Promoter/Pages'), for: 'App\\Filament\\Promoter\\Pages')
            ->discoverResources(in: app_path('Filament/Promoter/Resources'), for: 'App\\Filament\\Promoter\\Resources')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Promoter/Widgets'), for: 'App\\Filament\\Promoter\\Widgets')
            ->widgets([
                // Widgets\FilamentInfoWidget::class,
                // Widgets\AccountWidget::class,
                PromoterStats::class,
                MostUsedPromoCode::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                // FilamentDeveloperLoginsPlugin::make()
                // ->enabled(config('app.debug', false))
                // ->users($promoters->pluck('email', 'name')->toArray()),
            ]);
    }
}
