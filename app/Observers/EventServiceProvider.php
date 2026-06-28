<?php
// app/Providers/EventServiceProvider.php

namespace App\Providers;

use App\Events\{
    UserRegistered,
    VaccinationDue,
    StockLow,
    WeightLossDetected,
    MessageSent,
    CommentAdded,
    LikeAdded,
    ShareAdded,
    PublicationReported,
    ReportResolved,
};
use App\Listeners\{
    SendWelcomeNotification,
    SendVaccinationReminder,
    SendStockLowAlert,
    SendWeightLossAlert,
    SendMessageNotification,
    SendCommentNotification,
    SendLikeNotification,
    SendShareNotification,
    SendPublicationReportedAlert,
    SendReportResolvedAlert,
};
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeNotification::class,
        ],
        VaccinationDue::class => [
            SendVaccinationReminder::class,
        ],
        StockLow::class => [
            SendStockLowAlert::class,
        ],
        WeightLossDetected::class => [
            SendWeightLossAlert::class,
        ],
        MessageSent::class => [
            SendMessageNotification::class,
        ],
        CommentAdded::class => [
            SendCommentNotification::class,
        ],
        LikeAdded::class => [
            SendLikeNotification::class,
        ],
        ShareAdded::class => [
            SendShareNotification::class,
        ],
        PublicationReported::class => [
            SendPublicationReportedAlert::class,
        ],
        ReportResolved::class => [
            SendReportResolvedAlert::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}