<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

use function Safe\json_encode;

class RabbitMQPublisher
{
    /**
     * Create a new class instance.
     */
    private AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')
        );
    }

    public function publish(
        string $routingKey,
        array $payload
    ): void {

        $channel = $this->connection->channel();

        $channel->exchange_declare(
            config('rabbitmq.exchange'),
            'topic',
            false,
            true,
            false
        );

        $message = new AMQPMessage(
            json_encode($payload),
            [
                'content_type' => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]
        );

        $channel->basic_publish(
            $message,
            config('rabbitmq.exchange'),
            $routingKey
        );

        $channel->close();
    }

    public function __destruct()
    {
        if(isset($asset->connection)) {
            $this->connection->close();
        }
        
    }
}
