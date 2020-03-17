<?php

declare(strict_types=1);

use Symfony\Component\Console\Application;

define('APP_CONSOLE', true);

$container = require_once __DIR__. '/../config/bootstrap.php';

$cli = new Application('Application Console');

$commands = [
    App\EventConsumeCommand::class,
    App\EventGenerateCommand::class
];

foreach ($commands as $command) {
    $cli->add($container->get($command));
}

$exitCode = $cli->run();

exit($exitCode);
