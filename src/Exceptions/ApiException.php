<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Exceptions;

final class ApiException extends BaleBotException
{
    public function __construct(
        string $message,
        private readonly ?int $errorCode = null,
        private readonly array $parameters = [],
    ) {
        parent::__construct($message, $errorCode ?? 0);
    }

    public function errorCode(): ?int
    {
        return $this->errorCode;
    }

    public function parameters(): array
    {
        return $this->parameters;
    }
}
