<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Contracts;

use OneTwoThree\BaleBot\Support\Update;

interface UserResolverInterface
{
    public function resolveUserId(Update $update): string|int|null;
}
