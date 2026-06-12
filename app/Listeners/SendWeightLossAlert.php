<?php
// app/Listeners/SendWeightLossAlert.php

namespace App\Listeners;

use App\Events\WeightLossDetected;
use App\Services\AlertService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWeightLossAlert implements ShouldQueue
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function handle(WeightLossDetected $event): void
    {
        $this->alertService->sendWeightLossAlert($event->animal, $event->poidsAvant, $event->poidsApres);
    }
}