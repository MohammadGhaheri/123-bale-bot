<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Storage;

use OneTwoThree\BaleBot\Contracts\StorageInterface;
use OneTwoThree\BaleBot\Exceptions\BaleBotException;

final class FileStorage implements StorageInterface
{
    public function __construct(private readonly string $path)
    {
        if (!is_file($this->path)) {
            file_put_contents($this->path, json_encode([], JSON_PRETTY_PRINT));
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $items = $this->read();
        return $items[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $items = $this->read();
        $items[$key] = $value;
        $this->write($items);
    }

    public function delete(string $key): void
    {
        $items = $this->read();
        unset($items[$key]);
        $this->write($items);
    }

    private function read(): array
    {
        $json = file_get_contents($this->path);
        $data = json_decode((string) $json, true);
        if (!is_array($data)) {
            throw new BaleBotException('Storage file contains invalid JSON.');
        }

        return $data;
    }

    private function write(array $items): void
    {
        file_put_contents($this->path, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
