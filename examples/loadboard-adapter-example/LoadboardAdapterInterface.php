<?php

declare(strict_types=1);

interface LoadboardAdapterInterface
{
    public function notifyDriver(int|string $chatId, array $load): void;

    public function findLoad(string $loadId): ?array;
}
