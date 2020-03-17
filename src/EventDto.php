<?php

declare(strict_types=1);

namespace App;

/**
 * EventDto.
 */
class EventDto implements \JsonSerializable
{
    private string $id;

    private string $account;

    private string $text;

    private ?string $afterEventId = null;

    public function __construct(string $id, string $account, string $text, ?string $afterEventId)
    {
        $this->id = $id;
        $this->account = $account;
        $this->text = $text;
        $this->afterEventId = $afterEventId;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['id'], $data['account'], $data['text'], $data['afterEventId']);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'account' => $this->account,
            'text' => $this->text,
            'afterEventId' => $this->afterEventId
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAfterEventId(): ?string
    {
        return $this->afterEventId;
    }
}
