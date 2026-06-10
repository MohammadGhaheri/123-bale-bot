<?php

declare(strict_types=1);

require_once __DIR__ . '/CrmTicketAdapterInterface.php';

final class DummyCrmTicketAdapter implements CrmTicketAdapterInterface
{
    public function createTicket(array $payload): string
    {
        return 'CRM-DEMO-' . substr(hash('sha256', json_encode($payload)), 0, 8);
    }
}
