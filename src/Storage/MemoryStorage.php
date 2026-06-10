<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Storage;

use OneTwoThree\BaleBot\Contracts\StorageInterface;

final class MemoryStorage implements StorageInterface
{
    public function __construct(private array $items = [])
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->items[$key] = $value;
    }

    public function delete(string $key): void
    {
        unset($this->items[$key]);
    }
}
