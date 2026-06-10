<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Contracts;

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Support\Update;

interface BotHandlerInterface
{
    public function handle(Update $update, BaleClient $client): void;
}
