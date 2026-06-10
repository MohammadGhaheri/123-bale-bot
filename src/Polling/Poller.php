<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Polling;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Router\Router;
use OneTwoThree\BaleBot\Support\Update;

final class Poller
{
    public function __construct(
        private readonly BaleClient $client,
        private readonly Router $router,
        private int $offset = 0,
    ) {
    }

    public function tick(int $timeout = 25, int $limit = 100): int
    {
        $updates = $this->client->getUpdates([
            'offset' => $this->offset > 0 ? $this->offset : null,
            'timeout' => $timeout,
            'limit' => $limit,
        ]);

        $count = 0;
        foreach ($updates as $payload) {
            if (!is_array($payload)) {
                continue;
            }

            $update = Update::fromArray($payload);
            $this->router->dispatch($update, $this->client);
            if ($update->id() !== null) {
                $this->offset = $update->id() + 1;
            }
            $count++;
        }

        return $count;
    }

    public function run(int $sleepMicroseconds = 250000): void
    {
        while (true) {
            $this->tick();
            usleep($sleepMicroseconds);
        }
    }
}
