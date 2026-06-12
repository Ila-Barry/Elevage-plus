<?php
// app/Listeners/SendVaccinationReminder.php

namespace App\Listeners;

use App\Events\VaccinationDue;
use App\Services\AlertService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVaccinationReminder implements ShouldQueue
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function handle(VaccinationDue $event): void
    {
        $this->alertService->sendVaccinationReminder($event->animal, $event->tache);
    }
}