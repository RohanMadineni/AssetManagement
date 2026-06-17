<?php

namespace App\Observers;
// namespace App\Services;

use App\Models\Asset;
use App\Services\ElasticsearchService;
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
        // $asset->load('category');
        app(ElasticsearchService::class)->indexAsset($asset);
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        //
        app(ElasticsearchService::class)->indexAsset($asset);
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        //
        app(ElasticsearchService::class)->delete($asset->id);
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
