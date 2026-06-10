<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Contracts;

interface StorageInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function delete(string $key): void;
}
