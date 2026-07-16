<?php

namespace App\Console\Commands;
// namespace App\Services;
use App\Services\ElasticsearchService;
use Illuminate\Console\Command;
use App\Models\Asset;


class ReindexAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reindex-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() : void
    {
        //
        $elastic = app(ElasticsearchService::class);

        $assets = Asset::with('category')->get();
        $elastic->deleteIndex();
        $elastic->createIndex();
        foreach ($assets as $asset) {
            
            $elastic->indexAsset($asset);
        }

        $this->info('Assets indexed successfully');
    }
}
