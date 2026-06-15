<?php
// app/Listeners/SendStockLowAlert.php

namespace App\Listeners;

use App\Events\StockLow;
use App\Services\AlertService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendStockLowAlert implements ShouldQueue
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    public function handle(StockLow $event): void
    {
        $this->alertService->sendStockCritiqueAlert($event->produit);
    }
}