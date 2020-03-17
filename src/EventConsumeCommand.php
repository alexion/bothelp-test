<?php

declare(strict_types=1);

namespace App;

use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * EventConsumeCommand.
 */
class EventConsumeCommand extends Command
{
    private QueueManager $queueManager;

    private EventProcessor $eventProcessor;

    public function __construct(QueueManager $queueManager, EventProcessor $eventProcessor)
    {
        parent::__construct();
        $this->queueManager = $queueManager;
        $this->eventProcessor = $eventProcessor;
    }

    protected function configure(): void
    {
        $this->setName('event:consume');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->queueManager->consume(function (AMQPMessage $message) use ($output) {
            $data = json_decode($message->body, true);
            $eventDto = EventDto::fromArray($data);
            return $this->eventProcessor->process($eventDto);
        });
    }
}
