<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Contracts;

use OneTwoThree\BaleBot\Support\Update;

interface MiddlewareInterface
{
    public function process(Update $update, callable $next): void;
}
