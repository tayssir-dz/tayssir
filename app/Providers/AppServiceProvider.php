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
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

use Spatie\Health\Facades\Health;

use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureObservers();
        $this->configureFilament();
        $this->configureScramble();
        $this->configureHealthChecks();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }

    private function configureObservers(): void
    {
        Card::observe(CardObserver::class);
        User::observe(UserObserver::class);
    }

    private function configureFilament(): void
    {
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

    private function configureScramble(): void
    {
        Scramble::registerApi('v1', [
            'api_path' => 'api/v1',
            'export_path' => 'public/v1.json',
            "ui" => [
                "title" => "Tayssir API v1",
                "theme" => "light",
                'hide_schemas' => true,
                'logo' => '/favicon.svg',
                'layout' => 'responsive',
            ],
        ])
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(SecurityScheme::http('bearer'));
            });

        Scramble::registerApi('v2', [
            'api_path' => 'api/v2',
            'export_path' => 'public/v2.json',
            "ui" => [
                "title" => "Tayssir API v2",
                "theme" => "light",
                'hide_schemas' => true,
                'logo' => '/favicon.svg',
                'layout' => 'responsive',
            ],
        ])
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(SecurityScheme::http('bearer'));
            });

        Scramble::registerUiRoute('docs/v1', api: 'v1');
        Scramble::registerUiRoute('docs/v2', api: 'v2');
    }

    private function configureHealthChecks(): void
    {
        Health::checks([
            UsedDiskSpaceCheck::new(),
            DatabaseCheck::new(),
            CacheCheck::new(),
            OptimizedAppCheck::new(),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
        ]);
    }
}
