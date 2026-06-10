<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Webhook;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Exceptions\BaleBotException;
use OneTwoThree\BaleBot\Router\Router;
use OneTwoThree\BaleBot\Support\Update;

final class WebhookHandler
{
    public function __construct(private readonly Router $router, private readonly BaleClient $client)
    {
    }

    public function handleJson(string $json, array $server = []): void
    {
        $payload = json_decode($json, true);
        if (!is_array($payload)) {
            throw new BaleBotException('Invalid webhook JSON payload.');
        }

        $this->router->dispatch(Update::fromArray($payload), $this->client);
    }
}
