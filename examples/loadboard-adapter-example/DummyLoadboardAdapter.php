<?php

declare(strict_types=1);

require_once __DIR__ . '/LoadboardAdapterInterface.php';

final class DummyLoadboardAdapter implements LoadboardAdapterInterface
{
    public array $notifications = [];

    public function notifyDriver(int|string $chatId, array $load): void
    {
        $this->notifications[] = ['chat_id' => $chatId, 'load' => $load];
    }

    public function findLoad(string $loadId): ?array
    {
        return [
            'id' => $loadId,
            'origin' => 'Demo origin',
            'destination' => 'Demo destination',
            'status' => 'available',
        ];
    }
}
