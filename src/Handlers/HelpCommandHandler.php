<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Handlers;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Support\Update;

final class HelpCommandHandler implements BotHandlerInterface
{
    public function __construct(private readonly string $message = "Available commands:\n/start - Start the bot\n/help - Show help")
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
