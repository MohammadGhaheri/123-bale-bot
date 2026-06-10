<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Handlers;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Support\Update;

final class StartCommandHandler implements BotHandlerInterface
{
    public function __construct(private readonly string $message = 'Welcome. Send /help to see available commands.')
    {
    }

    public function handle(Update $update, BaleClient $client): void
    {
        $chatId = $update->chatId();
        if ($chatId !== null) {
            $client->sendMessage($chatId, $this->message);
        }
    }
}
