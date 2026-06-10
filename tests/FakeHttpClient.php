<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Tests;

use OneTwoThree\BaleBot\Contracts\HttpClientInterface;

final class FakeHttpClient implements HttpClientInterface
{
    public array $requests = [];

    public function __construct(private readonly array $responses = [])
    {
    }

    public function request(string $method, string $url, array $options = []): array
    {
        $this->requests[] = compact('method', 'url', 'options');

        return $this->responses[count($this->requests) - 1] ?? [
            'status' => 200,
            'headers' => [],
            'body' => '{"ok":true,"result":true}',
        ];
    }
}
