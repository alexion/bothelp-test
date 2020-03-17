<?php

declare(strict_types=1);

namespace App;

use Psr\Log\LoggerInterface;
use Redis;

/**
 * EventProcessor.
 */
class EventProcessor
{
    private Redis $redis;

    private LoggerInterface $logger;

    public function __construct(Redis $redis, LoggerInterface $logger)
    {
        $this->redis = $redis;
        $this->logger = $logger;
    }

    public function process(EventDto $eventDto): bool
    {
        $processedEventId = $this->getProcessedEventIdForAccount($eventDto->getAccount());
        if ($processedEventId !== $eventDto->getAfterEventId()) {
            return false;
        }

        $this->logger->info('start process event', [$eventDto->getId()]);
        sleep(1);
        $this->logger->info('end process event', [$eventDto->getId()]);

        $this->storeProcessedEventIdForAccount($eventDto->getAccount(), $eventDto->getId());

        return true;
    }

    public function getLastEventIdForAccount(string $account): ?string
    {
        $key = $this->generateLastEventIdForAccountKey($account);
        return $this->redis->exists($key) ? $this->redis->get($key) : null;
    }

    public function storeLastEventIdForAccount(string $account, string $id): void
    {
        $this->redis->set(
            $this->generateLastEventIdForAccountKey($account),
            $id
        );
    }

    public function getProcessedEventIdForAccount(string $account): ?string
    {
        $key = $this->generateProcessedEventIdForAccountKey($account);
        return $this->redis->exists($key) ? $this->redis->get($key) : null;
    }

    public function storeProcessedEventIdForAccount(string $account, string $id): void
    {
        $this->redis->set(
            $this->generateProcessedEventIdForAccountKey($account),
            $id
        );
    }

    private function generateLastEventIdForAccountKey(string $account): string
    {
        return 'last-event-id-' . $account;
    }

    private function generateProcessedEventIdForAccountKey(string $account): string
    {
        return 'processed-event-id-' . $account;
    }
}
