<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Support\Update;

require_once __DIR__ . '/CrmTicketAdapterInterface.php';

final class CrmTicketHandler implements BotHandlerInterface
{
    public function __construct(private readonly CrmTicketAdapterInterface $adapter)
    {
    }

    public function handle(Update $update, BaleClient $client): void
    {
        $chatId = $update->chatId();
        $text = $update->text();

        if ($chatId === null || $text === null) {
            return;
        }

        $ticketId = $this->adapter->createTicket([
            'chat_id' => $chatId,
            'text' => mb_substr($text, 0, 2000),
            'source' => 'bale',
        ]);

        $client->sendMessage($chatId, 'CRM demo ticket created: ' . $ticketId);
    }
}
