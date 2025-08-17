<?php

namespace App\Providers;

use App\Models\Card;
use App\Models\User;
use App\Observers\CardObserver;
use App\Observers\UserObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::registerRenderHook('panels::body.end', fn (): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Card::observe(CardObserver::class);
        User::observe(UserObserver::class);
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en', 'fr']); // also accepts a closure
            // ->flags([
            //     'ar' => asset('flags/algeria.svg'),
            //     'fr' => asset('flags/france.svg'),
            //     'en' => asset('flags/usa.svg'),
            // ])
            // ->circular()
        });
    }
}
