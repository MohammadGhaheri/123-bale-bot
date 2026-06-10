<?php

declare(strict_types=1);

interface CrmTicketAdapterInterface
{
    /**
     * @param array{chat_id:int|string,text:string,source:string} $payload
     */
    public function createTicket(array $payload): string;
}
