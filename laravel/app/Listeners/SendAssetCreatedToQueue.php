<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AssetCreated;
use App\Jobs\ProcessAssetCreated;

class SendAssetCreatedToQueue
{
    /**
     * Create the event listener.
     */
    // public function __construct()
    // {
    //     //
    // }

    /**
     * Handle the event.
     */
    public function handle(AssetCreated $event): void
    {
        //
        ProcessAssetCreated::dispatch($event);
    }
}
