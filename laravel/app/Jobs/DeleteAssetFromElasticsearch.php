<?php

namespace App\Jobs;

use App\Services\ElasticsearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeleteAssetFromElasticsearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $assetId) {}

    public function handle(ElasticsearchService $es): void
    {
        $es->delete($this->assetId);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Elasticsearch delete failed', [
            'asset_id' => $this->assetId,
            'error' => $e->getMessage(),
        ]);
    }
}
