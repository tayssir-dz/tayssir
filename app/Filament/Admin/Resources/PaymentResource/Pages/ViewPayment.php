<?php

namespace App\Filament\Admin\Resources\PaymentResource\Pages;

use App\Enums\Purchase\PaymentStatus;
use App\Enums\Purchase\PaymentType;
use App\Filament\Admin\Resources\PaymentResource;
use App\Notifications\Purchase\ManualPaymentSucceeded;
use App\Notifications\Purchase\ManualPaymentFailed;
use App\Services\Purchase\SubscriptionActivationService;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();

        $canModerate = $record->payment_type === PaymentType::MANUAL
            && $record->status === PaymentStatus::PENDING;

        if (! $canModerate) {
            return [];
        }

        return [
            Actions\Action::make('accept')
                ->label(__('custom.models.payment.actions.accept'))
                ->color('success')
                ->requiresConfirmation()
                ->action(function () use ($record) {
                    $record->status = PaymentStatus::ACCEPTED;
                    $record->save();
                    $success = SubscriptionActivationService::ActivateSubscriptionForUser($record->subscription_id, $record->user_id);
                    if ($success) {
                        $record->user->notify(new ManualPaymentSucceeded($record->subscription->name));
                        Notification::make()
                            ->title(__('custom.models.payment.notices.accepted'))
                            ->success()
                            ->send();
                    } else {
                        $record->status = PaymentStatus::PENDING;
                        $record->save();
                        Notification::make()
                            ->title(__('something went wrong'))
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('reject')
                ->label(__('custom.models.payment.actions.reject'))
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('rejection_reason')
                        ->label(__('custom.models.payment.rejection_reason'))
                        ->required()
                        ->rows(3),
                ])
                ->requiresConfirmation()
                ->action(function (array $data) use ($record) {
                    $record->status = PaymentStatus::REJECTED;
                    $record->rejection_reason = $data['rejection_reason'];
                    $record->save();

                    $record->user->notify(new ManualPaymentFailed($data['rejection_reason'] ?? null, $record->subscription->name ?? ''));

                    Notification::make()
                        ->title(__('custom.models.payment.notices.rejected'))
                        ->danger()
                        ->send();
                }),
        ];
    }

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make(__('custom.models.payment.sections.basic_info'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('ID'),
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->label(__('custom.table.created_at')),
                                TextEntry::make('status')
                                    ->badge()
                                    ->label(__('custom.models.payment.status')),
                                TextEntry::make('payment_type')
                                    ->badge()
                                    ->label(__('custom.models.payment.type')),
                                TextEntry::make('user.email')
                                    ->label(__('custom.models.user.email'))
                                    ->url(fn($record) => route('filament.dashboard.resources.users.edit', $record->user_id))
                                    ->openUrlInNewTab()
                                    ->color('primary'),
                                TextEntry::make('subscription.name')
                                    ->label(__('custom.models.subscription'))
                                    ->url(fn($record) => $record->subscription_id ? route('filament.dashboard.resources.subscriptions.edit', $record->subscription_id) : null)
                                    ->openUrlInNewTab()
                                    ->color('primary'),
                            ]),
                    ]),

                Section::make(__('custom.models.payment.sections.amounts'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('price')
                                    ->label(__('custom.models.payment.price'))
                                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format((float) $state, 2) . ' DZD')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('final_price')
                                    ->label(__('custom.models.payment.final_price'))
                                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format((float) $state, 2) . ' DZD')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('discount_percentage')
                                    ->label(__('custom.models.payment.discount_percentage'))
                                    ->suffix('%')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('discount_amount')
                                    ->label(__('custom.models.payment.discount_amount'))
                                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format((float) $state, 2) . ' DZD')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('promocode_percentage')
                                    ->label(__('custom.models.payment.promocode_percentage'))
                                    ->suffix('%')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('promocode_amount')
                                    ->label(__('custom.models.payment.promocode_amount'))
                                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format((float) $state, 2) . ' DZD')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('combined_discount_percentage')
                                    ->label(__('custom.models.payment.combined_discount_percentage'))
                                    ->suffix('%')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('combined_discount_amount')
                                    ->label(__('custom.models.payment.combined_discount_amount'))
                                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format((float) $state, 2) . ' DZD')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),

                Section::make(__('custom.models.payment.sections.promoter'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('promoter_margin_percentage')
                                    ->label(__('custom.models.payment.promoter_margin_percentage'))
                                    ->suffix('%')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('promoter_margin_amount')
                                    ->label(__('custom.models.payment.promoter_margin_amount'))
                                    ->formatStateUsing(fn($state) => is_null($state) ? '-' : number_format((float) $state, 2) . ' DZD')
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ]),

                Section::make(__('custom.models.payment.sections.extras'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('promoCode.code')
                                    ->placeholder(__('custom.models.generic.empty'))
                                    ->label(__('custom.models.promoCode.code')),
                                TextEntry::make('attachment')
                                    ->label(__('custom.models.payment.attachment'))
                                    ->visible(fn($record) => (bool) $record->getFirstMedia('attachment'))
                                    ->html()
                                    ->state("___")
                                    ->formatStateUsing(function ($state, $record) {
                                        $href = url('/admin/payments/' . $record->getKey() . '/attachment');
                                        $text = __('custom.models.payment.view_attachment');
                                        return '<a href="' . e($href) . '" target="_blank" class="text-primary-600 hover:underline">' . e($text) . '</a>';
                                    }),
                            ]),
                    ]),

                Section::make(__('custom.models.payment.sections.metadata'))
                    ->schema([
                        KeyValueEntry::make('metadata')
                            ->label(__('custom.models.payment.metadata')),
                    ]),
            ]);
    }
}
