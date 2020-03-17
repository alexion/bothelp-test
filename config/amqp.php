<?php

declare(strict_types=1);

use PhpAmqpLib\Connection\AMQPSSLConnection;
use Psr\Container\ContainerInterface;
use DI\ContainerBuilder;
use PhpAmqpLib\Connection\AMQPStreamConnection;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        AMQPStreamConnection::class => function (ContainerInterface $container) {
            return new AMQPSSLConnection(
                'rabbit-mq',
                5672,
                'rabbitmq',
                'rabbitmq',
                'vhost',
                [],
                [
                    'read_timeout' => 60,
                    'write_timeout' => 60,
                    'connect_timeout' => 60,
                ]
            );
        }
    ]);
};
