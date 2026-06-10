<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Client;

use OneTwoThree\BaleBot\Contracts\HttpClientInterface;
use OneTwoThree\BaleBot\Exceptions\BaleBotException;

final class NativeHttpClient implements HttpClientInterface
{
    public function request(string $method, string $url, array $options = []): array
    {
        $headers = $options['headers'] ?? [];
        $body = $options['body'] ?? null;
        $timeout = (int) ($options['timeout'] ?? 30);

        if (function_exists('curl_init')) {
            return $this->curlRequest($method, $url, $headers, $body, $timeout);
        }

        return $this->streamRequest($method, $url, $headers, $body, $timeout);
    }

    private function curlRequest(string $method, string $url, array $headers, ?string $body, int $timeout): array
    {
        $handle = curl_init($url);
        if ($handle === false) {
            throw new BaleBotException('Unable to initialize HTTP client.');
        }

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_HTTPHEADER => $this->formatHeaders($headers),
            CURLOPT_HEADER => true,
        ]);

        if ($body !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }

        $raw = curl_exec($handle);
        if ($raw === false) {
            $message = curl_error($handle);
            curl_close($handle);
            throw new BaleBotException('HTTP request failed: ' . $message);
        }

        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $headerSize = (int) curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        curl_close($handle);

        return [
            'status' => $status,
            'headers' => [],
            'body' => substr((string) $raw, $headerSize),
        ];
    }

    private function streamRequest(string $method, string $url, array $headers, ?string $body, int $timeout): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => strtoupper($method),
                'header' => implode("\r\n", $this->formatHeaders($headers)),
                'content' => $body ?? '',
                'timeout' => $timeout,
                'ignore_errors' => true,
            ],
        ]);

        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            throw new BaleBotException('HTTP request failed.');
        }

        $status = 0;
        foreach ($http_response_header ?? [] as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d+)/', $header, $match)) {
                $status = (int) $match[1];
                break;
            }
        }

        return ['status' => $status, 'headers' => [], 'body' => $response];
    }

    private function formatHeaders(array $headers): array
    {
        $formatted = [];
        foreach ($headers as $name => $value) {
            $formatted[] = $name . ': ' . $value;
        }

        return $formatted;
    }
}
