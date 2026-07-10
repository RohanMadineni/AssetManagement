// import { io } from './server.js';
import amqp from 'amqplib';
// const amqp = require('amqplib');

async function startRealtimeConsumer(io) {

    const connection = await amqp.connect(`amqp://${process.env.RABBITMQ_USER}:${process.env.RABBITMQ_PASSWORD}@${process.env.RABBITMQ_HOST}:${process.env.RABBITMQ_PORT}`);

    const channel = await connection.createChannel();

    const exchange = process.env.RABBITMQ_EXCHANGE;
    const queue = "realtime.queue";

    await channel.assertExchange(exchange, 'topic', {
        durable: true
    });

    await channel.assertQueue(queue,
        {
            durable: true
        }
    );

    await channel.bindQueue(
        queue,
        exchange,
        "asset.created"
    );
    await channel.bindQueue(
        queue,
        exchange,
        "asset.deleted"
    );
    await channel.bindQueue(
        queue,
        exchange,
        "asset.updated"
    );
    await channel.bindQueue(
        queue,
        exchange,
        "asset.assigned"
    );
    await channel.bindQueue(
        queue,
        exchange,
        "asset.returned"
    );

    channel.consume(
        queue,
        async(message)=>{
            
            if(message === null)
                return;

            try{

                const event =  JSON.parse(
                    message.content.toString()
                );

                if(event.event === "asset.created" || event.event === "asset.deleted" || event.event === "asset.updated"|| event.event === "asset.assigned") {
                    io.to(
                        `${event.user_id}`
                    )
                    .emit(
                        'notification',
                        event
                    );
                }

                channel.ack(message);

            } catch(error) {
                channel.nack(
                    message,
                    false,
                    true
                )
            }
        }
    );
}

export default startRealtimeConsumer;