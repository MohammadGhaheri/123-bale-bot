<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Contracts\StorageInterface;
use OneTwoThree\BaleBot\Support\Update;

final class SupportTicketHandler implements BotHandlerInterface
{
    public function __construct(private readonly StorageInterface $storage)
    {
    }

    public function handle(Update $update, BaleClient $client): void
    {
        $chatId = $update->chatId();
        $text = trim((string) $update->text());

        if ($chatId === null || $text === '') {
            return;
        }

        $ticketId = 'TCK-' . date('Ymd') . '-' . random_int(1000, 9999);
        $this->storage->set($ticketId, [
            'chat_id' => $chatId,
            'text' => mb_substr($text, 0, 2000),
            'created_at' => date(DATE_ATOM),
        ]);

        $client->sendMessage($chatId, 'Your support request was registered. Tracking code: ' . $ticketId);
    }
}
