// import { io } from './server.js';
import amqp from 'amqplib';
// const amqp = require('amqplib');

async function connectRabbitMQ() {

    while (true) {

        try {

            console.log("Connecting to RabbitMQ...");

            const connection = await amqp.connect(
                `amqp://${process.env.RABBITMQ_USER}:${process.env.RABBITMQ_PASSWORD}@${process.env.RABBITMQ_HOST}:${process.env.RABBITMQ_PORT}`
            );

            console.log("Connected to RabbitMQ");

            return connection;

        } catch (err) {

            console.log(
                "RabbitMQ unavailable. Retrying in 5 seconds..."
            );

            await new Promise(resolve =>
                setTimeout(resolve, 5000)
            );
        }
    }
}

async function startRealtimeConsumer(io) {

    // const connection = await amqp.connect(`amqp://${process.env.RABBITMQ_USER}:${process.env.RABBITMQ_PASSWORD}@${process.env.RABBITMQ_HOST}:${process.env.RABBITMQ_PORT}`);
    const connection = await connectRabbitMQ();
    const channel = await connection.createChannel();

    const exchange = process.env.RABBITMQ_EXCHANGE;
    const queue = "realtime.queue";

    const processedNotifications = new Set();

    await channel.assertExchange(exchange, 'topic', {
        durable: true
    });

    await channel.assertQueue(queue,
        {
            durable: true
        }
    );

    // await channel.bindQueue(
    //     queue,
    //     exchange,
    //     "asset.created"
    // );
    // await channel.bindQueue(
    //     queue,
    //     exchange,
    //     "asset.deleted"
    // );   
    // await channel.bindQueue(
    //     queue,
    //     exchange,
    //     "asset.updated"
    // );
    // await channel.bindQueue(
    //     queue,
    //     exchange,
    //     "asset.assigned"
    // );
    // await channel.bindQueue(
    //     queue,
    //     exchange,
    //     "asset.returned"
    // );
    await channel.bindQueue(
        queue,
        exchange,
        'notification.created'
    );
    

    channel.consume(
        queue,
        async(message)=>{
            
            if(message === null)
                return;

            try{
                const notification = JSON.parse(
                    message.content.toString()
                );
                
                const notificationId = notification.id;

                if (!notificationId) {
                    throw new Error(
                        "Notification has no ID"
                    );
                }


                // Already processed?
                if (processedNotifications.has(notificationId)) {

                    console.log(
                        `Duplicate notification ${notificationId} ignored`
                    );

                    channel.ack(message);
                    // return;
                }

                io.to(`${notification.user_id}`)
                .emit('notification', notification);
                // const event =  JSON.parse(
                //     message.content.toString()
                // );

                // if(event.event === "asset.created" || event.event === "asset.deleted" || event.event === "asset.updated"|| event.event === "asset.assigned"|| event.event === "asset.returned") {
                //     io.to(
                //         `${event.user_id}`
                //     )
                //     .emit(
                //         'notification',
                //         event
                //     );
                // }

                
                processedNotifications.add(
                    notificationId
                );


                console.log(
                    `Notification ${notificationId} processed`
                );
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