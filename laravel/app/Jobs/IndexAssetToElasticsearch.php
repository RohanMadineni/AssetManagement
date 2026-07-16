<?php

namespace App\Jobs;

use App\Services\ElasticsearchService;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class IndexAssetToElasticsearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public bool $afterCommit = true;
    public function __construct(public Asset $asset)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $es = app(ElasticsearchService::class);
        $es->indexAsset($this->asset);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Elasticsearch indexing failed', [
            'asset_id' => $this->asset->id,
            'error' => $e->getMessage(),
        ]);
    }
}
