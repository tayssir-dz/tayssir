<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Enums\QuestionScope;
use App\Filament\Widgets\MoneyStat;
use App\Models\User;
use App\Models\Card;
use App\Models\Chapter;
use App\Models\Division;
use App\Models\Material;
use App\Models\Question;
use App\Models\Subscription;
use App\Models\SubscriptionCard;
use App\Models\Unit;
use App\Models\UserAnswer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Spatie\FilamentSimpleStats\SimpleStat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class UsersCard extends BaseWidget
{
    use HasWidgetShield;

    // Set the maximum columns to 3 for better display
    protected string | array | int $columnSpan = 3;

    protected function getStats(): array
    {
        return [
            // User statistics
            SimpleStat::make(User::class)
                ->last30Days()
                ->dailyCount()
                ->label(__('stats.users.new'))
                ->description(__('stats.users.last30Days'))
                ->descriptionIcon('heroicon-o-users')
                ->color("success"),

            Stat::make(__('stats.users.total'), User::count())
                ->description(__('stats.users.overall'))
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([7, 10, 12, 15, 18, 20, User::count()])
                ->color('primary'),

            SimpleStat::make(User::class)
                ->lastDays(7)
                ->dailyCount()
                ->label(__('stats.users.thisWeek'))
                ->description(__('stats.users.last7Days'))
                ->descriptionIcon('heroicon-o-user-plus')
                ->color("info"),

            // Subscription statistics
            SimpleStat::make(Subscription::class)
                ->lastDays(30)
                ->dailyCount()
                ->label(__('stats.subscriptions.active'))
                ->description(__('stats.subscriptions.plans'))
                ->descriptionIcon('heroicon-o-credit-card')
                ->color("success"),

            SimpleStat::make(SubscriptionCard::class)
                ->lastDays(30)
                ->dailyCount()
                ->label(__('stats.subscriptions.cards'))
                ->description(__('stats.subscriptions.created'))
                ->descriptionIcon('heroicon-o-ticket')
                ->color("warning"),

            Stat::make(__('stats.subscriptions.redeemed'), SubscriptionCard::whereNotNull('redeemed_at')->count())
                ->description(__('stats.subscriptions.total_redeemed'))
                ->descriptionIcon('heroicon-o-check-badge')
                ->chart([2, 5, 8, 10, 15, SubscriptionCard::whereNotNull('redeemed_at')->count()])
                ->color('success'),

            // Material, Unit, and Chapter statistics  
            Stat::make(__('stats.materials.total'), Material::count())
                ->description(__('stats.materials.available'))
                ->descriptionIcon('heroicon-o-book-open')
                ->color('primary'),

            Stat::make(__('stats.units.total'), Unit::count())
                ->description(__('stats.units.available'))
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),

            Stat::make(__('stats.chapters.total'), Chapter::count())
                ->description(__('stats.chapters.available'))
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),

            // Question statistics
            Stat::make(__('stats.questions.total'), Question::count())
                ->description(__('stats.questions.all'))
                ->descriptionIcon('heroicon-o-question-mark-circle')
                ->chart([10, 20, 30, Question::count()])
                ->color('warning'),

            Stat::make(
                __('stats.questions.lesson'),
                Question::where('scope', QuestionScope::LESSON)->count()
            )
                ->description(__('stats.questions.lesson_desc'))
                ->descriptionIcon('heroicon-o-bookmark')
                ->color('info'),

            Stat::make(
                __('stats.questions.exercise'),
                Question::where('scope', QuestionScope::EXERCICE)->count()
            )
                ->description(__('stats.questions.exercise_desc'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('success'),

            // User engagement statistics
            SimpleStat::make(UserAnswer::class)
                ->lastDays(30)
                ->dailyCount()
                ->label(__('stats.answers.recent'))
                ->description(__('stats.answers.last30Days'))
                ->descriptionIcon('heroicon-o-pencil-square')
                ->color('primary'),

            Stat::make(
                __('stats.answers.points'),
                UserAnswer::sum('points_earned')
            )
                ->description(__('stats.answers.points_desc'))
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success'),

            // Division statistics
            Stat::make(__('stats.divisions.total'), Division::count())
                ->description(__('stats.divisions.available'))
                ->descriptionIcon('heroicon-o-building-library')
                ->color('info'),

            // Card statistics
            MoneyStat::make(Card::class)
                ->last30Days()
                ->dailySum('price')
                ->label(__('stats.cards.revenue'))
                ->description(__('stats.cards.revenue_desc'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            SimpleStat::make(Card::class)
                ->last30Days()
                ->dailyCount()
                ->label(__('stats.cards.created'))
                ->description(__('stats.cards.created_desc'))
                ->descriptionIcon('heroicon-o-document-plus')
                ->color('warning'),
        ];
    }
}
