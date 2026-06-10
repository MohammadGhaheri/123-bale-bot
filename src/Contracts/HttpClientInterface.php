<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Contracts;

interface HttpClientInterface
{
    /**
     * @return array{status:int, body:string, headers:array<string,string>}
     */
    public function request(string $method, string $url, array $options = []): array;
}
