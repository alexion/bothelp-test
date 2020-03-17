<?php

declare(strict_types=1);

use DI\ContainerBuilder;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

$amqp = require_once __DIR__ . '/../config/amqp.php';
$amqp($containerBuilder);

$logger = require_once __DIR__ . '/../config/logger.php';
$logger($containerBuilder);

$redis = require_once __DIR__ . '/../config/redis.php';
$redis($containerBuilder);

return $containerBuilder->build();
