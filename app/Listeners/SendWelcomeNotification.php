<?php
// app/Listeners/SendWelcomeNotification.php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\AlertService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeNotification implements ShouldQueue
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function handle(UserRegistered $event): void
    {
        $this->alertService->sendWelcomeAlert($event->user);
    }
}