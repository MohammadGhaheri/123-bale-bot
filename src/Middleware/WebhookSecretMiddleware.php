<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Middleware;

use OneTwoThree\BaleBot\Contracts\MiddlewareInterface;
use OneTwoThree\BaleBot\Exceptions\BaleBotException;
use OneTwoThree\BaleBot\Support\Update;

final class WebhookSecretMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ?string $expectedSecret, private readonly ?string $givenSecret)
    {
    }

    public function process(Update $update, callable $next): void
    {
        if ($this->expectedSecret !== null && !hash_equals($this->expectedSecret, (string) $this->givenSecret)) {
            throw new BaleBotException('Invalid webhook secret.');
        }

        $next($update);
    }
}
