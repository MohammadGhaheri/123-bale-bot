<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Support;

use OneTwoThree\BaleBot\Exceptions\InvalidConfigException;

final class BotConfig
{
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl = 'https://tapi.bale.ai',
        private readonly int $timeoutSeconds = 30,
        private readonly int $maxRetries = 1,
        private readonly ?string $webhookSecret = null,
        private readonly string $environment = 'production',
    ) {
        $this->validate();
    }

    public static function fromEnv(array $env): self
    {
        return new self(
            (string) ($env['BALE_BOT_TOKEN'] ?? ''),
            (string) ($env['BALE_API_BASE_URL'] ?? 'https://tapi.bale.ai'),
            (int) ($env['BALE_TIMEOUT'] ?? 30),
            (int) ($env['BALE_MAX_RETRIES'] ?? 1),
            isset($env['BALE_WEBHOOK_SECRET']) ? (string) $env['BALE_WEBHOOK_SECRET'] : null,
            (string) ($env['APP_ENV'] ?? 'production'),
        );
    }

    public function token(): string
    {
        return $this->token;
    }

    public function baseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    public function timeoutSeconds(): int
    {
        return $this->timeoutSeconds;
    }

    public function maxRetries(): int
    {
        return $this->maxRetries;
    }

    public function webhookSecret(): ?string
    {
        return $this->webhookSecret;
    }

    public function environment(): string
    {
        return $this->environment;
    }

    public function methodUrl(string $method): string
    {
        return $this->baseUrl() . '/bot' . $this->token . '/' . ltrim($method, '/');
    }

    private function validate(): void
    {
        if ($this->token === '' || !str_contains($this->token, ':')) {
            throw new InvalidConfigException('A Bale bot token is required and must look like "<id>:<secret>".');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidConfigException('The Bale API base URL must be a valid URL.');
        }

        if ($this->timeoutSeconds < 1) {
            throw new InvalidConfigException('Timeout must be at least one second.');
        }

        if ($this->maxRetries < 0 || $this->maxRetries > 5) {
            throw new InvalidConfigException('Max retries must be between 0 and 5.');
        }
    }
}
