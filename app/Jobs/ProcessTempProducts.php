<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\TempProduct;
use App\Jobs\SyncSuperseisProductDetail;

class ProcessTempProducts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tempProducts = TempProduct::where('status', 'pending')->limit(10)->get();

        foreach ($tempProducts as $tempProduct) {
            if ($tempProduct->store->name === 'Superseis') {
                SyncSuperseisProductDetail::dispatch($tempProduct)->delay(now()->addSeconds(5));
            }
        }
    }
}
