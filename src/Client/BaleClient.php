<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Client;

use OneTwoThree\BaleBot\Contracts\HttpClientInterface;
use OneTwoThree\BaleBot\Contracts\LoggerInterface;
use OneTwoThree\BaleBot\Exceptions\ApiException;
use OneTwoThree\BaleBot\Support\BotConfig;
use OneTwoThree\BaleBot\Support\NullLogger;
use OneTwoThree\BaleBot\Support\Redactor;

final class BaleClient
{
    public function __construct(
        private readonly BotConfig $config,
        private readonly ?HttpClientInterface $httpClient = null,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function getMe(): array
    {
        return $this->call('getMe');
    }

    public function sendMessage(int|string $chatId, string $text, array $options = []): array
    {
        return $this->call('sendMessage', array_merge($options, [
            'chat_id' => $chatId,
            'text' => $text,
        ]));
    }

    public function sendPhoto(int|string $chatId, string $photo, array $options = []): array
    {
        return $this->call('sendPhoto', array_merge($options, [
            'chat_id' => $chatId,
            'photo' => $photo,
        ]));
    }

    public function sendDocument(int|string $chatId, string $document, array $options = []): array
    {
        return $this->call('sendDocument', array_merge($options, [
            'chat_id' => $chatId,
            'document' => $document,
        ]));
    }

    public function answerCallbackQuery(string $callbackQueryId, array $options = []): array
    {
        return $this->call('answerCallbackQuery', array_merge($options, [
            'callback_query_id' => $callbackQueryId,
        ]));
    }

    public function getUpdates(array $options = []): array
    {
        return $this->call('getUpdates', $options);
    }

    public function setWebhook(string $url): bool
    {
        return (bool) $this->call('setWebhook', ['url' => $url]);
    }

    public function deleteWebhook(): bool
    {
        return (bool) $this->call('deleteWebhook');
    }

    public function getWebhookInfo(): array
    {
        return $this->call('getWebhookInfo');
    }

    public function getFile(string $fileId): array
    {
        return $this->call('getFile', ['file_id' => $fileId]);
    }

    public function call(string $method, array $params = []): mixed
    {
        $attempt = 0;
        beginning:
        $attempt++;

        $payload = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($payload === false) {
            throw new ApiException('Unable to encode Bale API request.');
        }

        $this->log()->debug('Bale API request', [
            'method' => $method,
            'params' => $params,
        ]);

        $response = $this->client()->request('POST', $this->config->methodUrl($method), [
            'timeout' => $this->config->timeoutSeconds(),
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
            ],
            'body' => $payload,
        ]);

        $decoded = json_decode($response['body'], true);
        if (!is_array($decoded)) {
            if ($attempt <= $this->config->maxRetries()) {
                goto beginning;
            }

            throw new ApiException('Bale API returned a non-JSON response.');
        }

        $this->log()->debug('Bale API response', [
            'method' => $method,
            'status' => $response['status'],
            'body' => Redactor::redact($decoded),
        ]);

        if (($decoded['ok'] ?? false) !== true) {
            $parameters = is_array($decoded['parameters'] ?? null) ? $decoded['parameters'] : [];
            throw new ApiException(
                (string) ($decoded['description'] ?? 'Bale API request failed.'),
                isset($decoded['error_code']) ? (int) $decoded['error_code'] : null,
                $parameters,
            );
        }

        return $decoded['result'] ?? true;
    }

    public function config(): BotConfig
    {
        return $this->config;
    }

    private function client(): HttpClientInterface
    {
        return $this->httpClient ?? new NativeHttpClient();
    }

    private function log(): LoggerInterface
    {
        return $this->logger ?? new NullLogger();
    }
}
