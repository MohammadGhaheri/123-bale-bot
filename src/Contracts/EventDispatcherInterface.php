<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Contracts;

interface EventDispatcherInterface
{
    public function dispatch(string $event, array $payload = []): void;
}
