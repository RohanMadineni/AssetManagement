<?php

namespace App\Jobs;

use App\Events\AssetCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessAssetCreated implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public AssetCreated $event)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $asset = $this->event->asset;

        Log::info('Processing asset.created event', [
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
        ]);

    }
}
