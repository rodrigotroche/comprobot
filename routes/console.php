<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncSuperseisPrices;
use App\Jobs\ProcessTempProducts;
use App\Jobs\ProcessStoreUrlsBatch;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::job(new ProcessStoreUrlsBatch(5))->everyFiveMinutes()->appendOutputTo(storage_path('logs/process-store-urls-batch.log'));
Schedule::job(new SyncSuperseisPrices())->everyTwoMinutes()->appendOutputTo(storage_path('logs/sync-superseis-prices.log'));
Schedule::job(new ProcessTempProducts())->everyMinute()->appendOutputTo(storage_path('logs/sync-superseis-prices.log'));
