<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use Exception;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;


class SubscriptionCardLib
{
    public static function CreateSubscriptionCards(array $data, $subscription)
    {
        // dd($s ubscription->id, $subscription->name, $data);
        $codes = [];
        for ($i = 0; $i < $data["number_of_cards"]; $i++) {
            $code = rand(100000000000, 999999999999);
            $codes = array_merge($codes, [$code]);
        }
        $subscription->subscriptionCards()->createMany(array_map(function ($code) {
            return [
                'code' => $code,
            ];
        }, $codes));
    }

    public static function getFilters()
    {
        return [
            Filter::make(__("custom.models.subscriptionCard.activated_cards"))
                ->query(fn(Builder $query): Builder => $query->where('user_id', '!=', null))
                ->label(__("custom.models.subscriptionCard.activated_cards")),
            Filter::make(__("custom.models.subscriptionCard.unactivated_cards"))
                ->query(fn(Builder $query): Builder => $query->where('user_id', '=', null))
                ->label(__("custom.models.subscriptionCard.unactivated_cards")),
        ];
    }

    public static function getHeaderActions($ownedRecord): array
    {
        return [
            Action::make('create-cards')
                // ->slideOver()
                ->label(__('custom.models.subscriptionCard.create_subscriptionCards'))
                ->visible(auth()->user()->can("create_subsciption_cards_subscription"))
                ->form([
                    TextInput::make("number_of_cards")
                        ->label(__("custom.models.subscriptionCard.number_of_cards"))
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                ])
                ->action(fn(array $data) => SubscriptionCardLib::CreateSubscriptionCards($data, $ownedRecord)),
        ];
    }

    public static function getActions(): array
    {
        return [
            Tables\Actions\Action::make('copy code')
                ->visible(auth()->user()->can("copy_card_code_subscription"))
                ->label(__("custom.models.subscriptionCard.copy_code"))
                ->icon('heroicon-s-clipboard-document-check')
                ->color("gray")
                ->action(function ($livewire, $record) {
                    // dd(["js" => 'window.navigator.clipboard.writeText("' . $record->code . '");']);
                    $livewire->js(
                        'window.navigator.clipboard.writeText("' . $record->code . '");'
                    );
                    Notification::make()
                        ->title(__("custom.models.subscriptionCard.code_copied"))
                        ->success()
                        ->send();
                }),
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make("user")
                    ->visible(fn($record) => blank($record->user_id) && auth()->user()->can("attach_user_subscription"))
                    ->label(__("custom.models.subscriptionCard.attach_user"))
                    ->icon('heroicon-s-user')
                    ->color("primary")
                    ->form([
                        TextInput::make("email")
                            ->label(__("custom.models.subscriptionCard.user.email"))
                            ->email()
                            ->required(),
                    ])
                    ->action(function (array $data, $record) {
                        $user = \App\Models\User::where("email", $data["email"])->first();
                        if ($user) {
                            try {
                                $record->update([
                                    "user_id" => $user->id,
                                    "redeemed_at" => now()
                                ]);
                                Notification::make()
                                    ->title(__("custom.models.subscriptionCard.user.added_successfully"))
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title(__("custom.models.subscriptionCard.user.already_subscribed"))
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            Notification::make()
                                ->title(__("custom.models.subscriptionCard.user.not_found"))
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make()->visible(auth()->user()->can("delete_card_subscription")),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\ViewAction::make()
            ])
        ];
    }
}
