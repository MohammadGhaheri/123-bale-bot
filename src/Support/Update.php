<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Support;

final class Update
{
    public function __construct(private readonly array $payload)
    {
    }

    public static function fromArray(array $payload): self
    {
        return new self($payload);
    }

    public function toArray(): array
    {
        return $this->payload;
    }

    public function id(): ?int
    {
        return isset($this->payload['update_id']) ? (int) $this->payload['update_id'] : null;
    }

    public function message(): ?array
    {
        return $this->payload['message'] ?? $this->payload['edited_message'] ?? null;
    }

    public function callbackQuery(): ?array
    {
        return $this->payload['callback_query'] ?? null;
    }

    public function chatId(): int|string|null
    {
        $message = $this->message();
        if (isset($message['chat']['id'])) {
            return $message['chat']['id'];
        }

        $callback = $this->callbackQuery();
        return $callback['message']['chat']['id'] ?? null;
    }

    public function text(): ?string
    {
        $message = $this->message();
        return isset($message['text']) ? trim((string) $message['text']) : null;
    }

    public function command(): ?string
    {
        $text = $this->text();
        if ($text === null || !str_starts_with($text, '/')) {
            return null;
        }

        $command = strtok(substr($text, 1), " \t\r\n@");
        return $command === false ? null : strtolower($command);
    }

    public function callbackData(): ?string
    {
        $callback = $this->callbackQuery();
        return isset($callback['data']) ? (string) $callback['data'] : null;
    }
}
