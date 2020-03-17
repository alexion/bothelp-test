<?php

declare(strict_types=1);

namespace App;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * QueueManager.
 */
class QueueManager
{
    const QUEUE = 'events';

    private AMQPStreamConnection $connection;

    private AMQPChannel $channel;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
        $this->channel = $connection->channel();
        $this->init();
    }

    public function push(string $message): void
    {
        $message = new AMQPMessage($message);
        $this->channel->basic_publish($message, self::QUEUE);
    }

    public function consume(callable $consumer): void
    {
        $consumerTag = 'cons_' . getmygid();
        $this->channel->basic_consume(self::QUEUE, $consumerTag, false, true, false, false, function (AMQPMessage $message) use ($consumer) {
            if (!$this->connection->isConnected()) {
                $this->connection->reconnect();
            }
            $result = $consumer($message);
            if (!$result) {
                $this->push($message->body);
            }
        });
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    private function init(): void
    {
        register_shutdown_function(
            function (AMQPChannel $channel, AMQPStreamConnection $connection) {
                $channel->close();
                $connection->close();
            },
            $this->channel,
            $this->connection
        );

        $this->channel->queue_declare(self::QUEUE, false, false, false, false);
        $this->channel->exchange_declare(self::QUEUE, 'direct', false, false, false);
        $this->channel->queue_bind(self::QUEUE, self::QUEUE);
    }
}
