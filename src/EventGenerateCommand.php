<?php

declare(strict_types=1);

namespace App;

use Ramsey\Uuid\Uuid;
use Redis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * EventGenerateCommand.
 */
class EventGenerateCommand extends Command
{
    private QueueManager $queueManager;

    private EventProcessor $eventProcessor;
    /**
     * @var Redis
     */
    private Redis $redis;

    public function __construct(QueueManager $queueManager, EventProcessor $eventProcessor, Redis $redis)
    {
        parent::__construct();
        $this->queueManager = $queueManager;
        $this->eventProcessor = $eventProcessor;
        $this->redis = $redis;
    }

    protected function configure(): void
    {
        $this->setName('event:generate');
        $this->addArgument('accounts', InputArgument::REQUIRED);
        $this->addArgument('events', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $accounts = (int)$input->getArgument('accounts');
        $events = (int)$input->getArgument('events');
        $this->redis->flushAll();
        $io->progressStart($events);
        for ($i = 0; $i < $events; $i++) {
            $account = 'account-' . mt_rand(1, $accounts);
            $eventDto = new EventDto(
                Uuid::uuid4()->toString(),
                $account,
                'text-' . $i,
                $this->eventProcessor->getLastEventIdForAccount($account)
            );
            $this->queueManager->push(json_encode($eventDto));
            $this->eventProcessor->storeLastEventIdForAccount($eventDto->getAccount(), $eventDto->getId());
            $io->progressAdvance();
        }
        $io->progressFinish();

        return 0;
    }
}
