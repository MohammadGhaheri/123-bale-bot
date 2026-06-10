<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Support\Update;

require_once __DIR__ . '/LoadboardAdapterInterface.php';

final class LoadDetailsHandler implements BotHandlerInterface
{
    public function __construct(private readonly LoadboardAdapterInterface $adapter)
    {
    }

    public function handle(Update $update, BaleClient $client): void
    {
        $chatId = $update->chatId();
        $loadId = trim(str_replace('/load', '', (string) $update->text()));

        if ($chatId === null || $loadId === '') {
            return;
        }

        $load = $this->adapter->findLoad($loadId);
        $client->sendMessage($chatId, $load === null ? 'Load not found.' : sprintf(
            "Load %s\nFrom: %s\nTo: %s\nStatus: %s",
            $load['id'],
            $load['origin'],
            $load['destination'],
            $load['status'],
        ));
    }
}
