<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Handlers;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Support\Update;

final class EchoHandler implements BotHandlerInterface
{
    public function handle(Update $update, BaleClient $client): void
    {
        $chatId = $update->chatId();
        $text = $update->text();

        if ($chatId !== null && $text !== null) {
            $client->sendMessage($chatId, $text);
        }
    }
}
