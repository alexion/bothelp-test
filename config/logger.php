<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function () {
            $logger = new Logger('app');
            $logger->pushProcessor(new UidProcessor());

            $handler = new StreamHandler(__DIR__ . '/../var/logs/log.log');
            $logger->pushHandler($handler);

            return $logger;
        }
    ]);
};
