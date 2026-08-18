<?php

namespace App\Observers;
// namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\Http;
use App\Services\ElasticsearchService;
use App\Jobs\IndexAssetToElasticsearch;
use App\Jobs\DeleteAssetFromElasticsearch;
use Illuminate\Support\Facades\Cache;
use App\Services\RabbitMQPublisher;
use Illuminate\Support\Facades\Log;
// use App\Services\ElasticsearchService as ServicesElasticsearchService;

class AssetObserver
{
    /**
     * Handle the Asset "created" event.
     */
    public bool $afterCommit = true;
    public function created(Asset $asset): void
    {
        //
        // app(ElasticsearchService::class)->indexAsset($asset);
        Cache::tags(['assets'])->flush();
        // IndexAssetToElasticsearch::dispatch($asset);
        $correlationId = request()->attributes->get('correlation_id');

        Log::info('Publishing asset.created event', [
            'asset_id' => $asset->id,
            'correlation_id' => $correlationId,
        ]);
        app(RabbitMQPublisher::class)->publish(
            'asset.created',
            [
                'event' => 'asset.created',
                'asset_id' => $asset->id,
                'name' => $asset->name,
                'brand' => $asset->brand,
                'status' => $asset->status,
                'price' => $asset->price,
                'user_id' => $asset->user_id,
                'title' => 'Asset Created',
                'message' => $asset->name,
                'asset' => $asset->toArray(),
                'correlation_id' => $correlationId,
            ]
        );
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        //
        // app(ElasticsearchService::class)->indexAsset($asset);
        Cache::tags(['assets'])->flush();
        // DeleteAssetFromElasticsearch::dispatch($asset->id);
        // IndexAssetToElasticsearch::dispatch($asset);
        app(RabbitMQPublisher::class)->publish(
            'asset.updated',
            [
                'event' => 'asset.updated',
                'asset_id' => $asset->id,
                'name' => $asset->name,
                'user_id' => $asset->user_id,
                'title' => 'Asset Updated',
                'message' => $asset->name,
                'asset' => $asset->toArray(),
            ]
        );
       
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        //
        // app(ElasticsearchService::class)->delete($asset->id);
        Cache::tags(['assets'])->flush();
        // DeleteAssetFromElasticsearch::dispatch($asset->id);
        $correlationId = request()->attributes->get('correlation_id');

        Log::info('Publishing asset.deleted event', [
            'asset_id' => $asset->id,
            'correlation_id' => $correlationId,
        ]);

        app(RabbitMQPublisher::class)->publish(
            'asset.deleted',
            [
                'event' => 'asset.deleted',
                'asset_id' => $asset->id,
                'name' => $asset->name,
                'user_id' => $asset->user_id,
                'title' => 'Asset Deleted',
                'message' => $asset->name,
                'asset' => $asset->toArray(),
                'correlation_id' => $correlationId,
            ]
        );
        
    }

    /**
     * Handle the Asset "restored" event.
     */
    public function restored(Asset $asset): void
    {
        //
    }

    /**
     * Handle the Asset "force deleted" event.
     */
    public function forceDeleted(Asset $asset): void
    {
        //
    }
}
