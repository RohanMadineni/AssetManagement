<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Services\ElasticsearchService;
use App\Jobs\IndexAssetToElasticsearch;
use App\Jobs\DeleteAssetFromElasticsearch;

class ConsumeAssetNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume asset events and create notifications';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'),
        );

        $channel = $connection->channel();
        $exchange = config('rabbitmq.exchange');
        $queue = 'notification.queue';

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

        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,

            function (AMQPMessage $message) {

                try {

                    $event = json_decode(
                        $message->getBody(),
                        true
                    );
                    
                    if ($event['event'] === 'asset.created') {

                        Notification::create([
                            'user_id' => $event['user_id'],
                            'title' => 'Asset Created',
                            'message' => $event['name'],
                            'type' => 'success'
                        ]);
                        $this->info(
                            'Notification created'
                        );  
                    } 
                    else if ($event['event'] === 'asset.deleted') {

                        Notification::create([
                            'user_id' => $event['user_id'],
                            'title' => 'Asset Deleted',
                            'message' => $event['name'],
                            'type' => 'success'
                        ]);
                        $this->info(
                            'Notification Created'
                        );  
                    }
                    else if ($event['event'] === 'asset.updated') {

                        Notification::create([
                            'user_id' => $event['user_id'],
                            'title' => 'Asset Updated',
                            'message' => $event['name'],
                            'type' => 'success'
                        ]);
                        $this->info(
                            'Notification Created'
                        );  
                    }
                    else if ($event['event'] === 'asset.assigned') {

                        Notification::create([
                            'user_id' => $event['user_id'],
                            'title' => 'Asset Assigned',
                            'message' => $event['name'],
                            'type' => 'success'
                        ]);
                        $this->info(
                            'Notification Created'
                        );  
                    }
                    else if ($event['event'] === 'asset.returned') {

                        Notification::create([
                            'user_id' => $event['user_id'],
                            'title' => $event['title'],
                            'message' => $event['message'],
                            'type' => 'success'
                        ]);
                        $this->info(
                            'Notification Created'
                        );  
                    }
                
                    $message->ack();

                } catch (\Exception $e) {

                    $this->error(
                        $e->getMessage()
                    );
                    
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
