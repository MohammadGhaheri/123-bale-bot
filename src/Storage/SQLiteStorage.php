<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Storage;

use OneTwoThree\BaleBot\Contracts\StorageInterface;
use PDO;

final class SQLiteStorage implements StorageInterface
{
    private PDO $pdo;

    public function __construct(string $path)
    {
        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS bale_bot_storage (key TEXT PRIMARY KEY, value TEXT NOT NULL)');
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $statement = $this->pdo->prepare('SELECT value FROM bale_bot_storage WHERE key = :key');
        $statement->execute(['key' => $key]);
        $value = $statement->fetchColumn();

        return $value === false ? $default : json_decode((string) $value, true);
    }

    public function set(string $key, mixed $value): void
    {
        $statement = $this->pdo->prepare('REPLACE INTO bale_bot_storage (key, value) VALUES (:key, :value)');
        $statement->execute([
            'key' => $key,
            'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function delete(string $key): void
    {
        $statement = $this->pdo->prepare('DELETE FROM bale_bot_storage WHERE key = :key');
        $statement->execute(['key' => $key]);
    }
}
