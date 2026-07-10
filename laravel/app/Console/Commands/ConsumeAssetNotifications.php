<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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

        $channel->queue_declare(
            'notification.queue',
            false,
            true,
            false,
            false
        );

        $channel->basic_consume(
            'notification.queue',
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
                            'user_id' => $event->user_id,
                            'title' => 'Asset Created',
                            'message' => $event->name,
                            'type' => 'success'
                        ]);
                        $this->info(
                            'Notification created'
                        );  
                    } 
                    else if ($event['event'] === 'asset.deleted') {

                        Notification::create([
                            'user_id' => $event->user_id,
                            'title' => 'Asset Deleted',
                            'message' => $event->name,
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
    }
}
