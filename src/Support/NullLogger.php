<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Support;

use OneTwoThree\BaleBot\Contracts\LoggerInterface;

final class NullLogger implements LoggerInterface
{
    public array $records = [];

    public function debug(string $message, array $context = []): void
    {
        $this->record('debug', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->record('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->record('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->record('error', $message, $context);
    }

    private function record(string $level, string $message, array $context): void
    {
        $this->records[] = [
            'level' => $level,
            'message' => $message,
            'context' => Redactor::redact($context),
        ];
    }
}
