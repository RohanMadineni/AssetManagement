<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Services\ElasticsearchService;
use App\Models\Asset;

class ConsumeElasticQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consume-elastic-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'),
        );

        $channel = $connection->channel();
        $exchange = config('rabbitmq.exchange');
        $queue = 'elasticsearch.queue';

        $channel->exchange_declare(
            $exchange,
            'topic',
            false,
            true,
            false
        );

        $channel->queue_declare(
            $queue,
            false,
            true,
            false,
            false
        );

        $channel->queue_bind(
            $queue,
            $exchange,
            'asset.created'
        );

        $channel->queue_bind(
            $queue,
            $exchange,
            'asset.updated'
        );

        $channel->queue_bind(
            $queue,
            $exchange,
            'asset.deleted'
        );

        $channel->queue_bind(
            $queue,
            $exchange,
            'asset.assigned'
        );

        $channel->queue_bind(
            $queue,
            $exchange,
            'asset.returned'
        );

        $elasticsearch = app(ElasticsearchService::class);

        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($elasticsearch) {

                try {

                    $event = json_decode(
                        $message->getBody(),
                        true
                    );

                    switch ($event['event']) {

                        case 'asset.created':
                        case 'asset.updated':

                            $asset = new Asset();
                            $asset->forceFill(
                                $event['asset']
                            );

                            $elasticsearch->indexAsset($asset);

                            break;

                        case 'asset.deleted':

                            $elasticsearch->delete(
                                $event['asset']['id']
                            );

                            break;
                    }

                    $message->ack();

                } catch (\Throwable $e) {

                    $this->error($e->getMessage());

                    $message->nack(
                        false,
                        true
                    );
                }

            }
        );
         while ($channel->is_consuming()) {

            $channel->wait();

        }

        $channel->close();
        $connection->close();
    }
}
