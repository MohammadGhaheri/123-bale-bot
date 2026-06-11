<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Otp;

use OneTwoThree\BaleBot\Exceptions\InvalidConfigException;

final class OtpConfig
{
    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $baseUrl = 'https://safir.bale.ai/api/v2',
        private readonly string $scope = 'read',
        private readonly int $timeoutSeconds = 30,
    ) {
        $this->validate();
    }

    public static function fromEnv(array $env): self
    {
        return new self(
            (string) ($env['BALE_OTP_CLIENT_ID'] ?? ''),
            (string) ($env['BALE_OTP_CLIENT_SECRET'] ?? ''),
            (string) ($env['BALE_OTP_BASE_URL'] ?? 'https://safir.bale.ai/api/v2'),
            (string) ($env['BALE_OTP_SCOPE'] ?? 'read'),
            (int) ($env['BALE_OTP_TIMEOUT'] ?? 30),
        );
    }

    public function clientId(): string
    {
        return $this->clientId;
    }

    public function clientSecret(): string
    {
        return $this->clientSecret;
    }

    public function baseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    public function scope(): string
    {
        return $this->scope;
    }

    public function timeoutSeconds(): int
    {
        return $this->timeoutSeconds;
    }

    public function url(string $path): string
    {
        return $this->baseUrl() . '/' . ltrim($path, '/');
    }

    private function validate(): void
    {
        if ($this->clientId === '') {
            throw new InvalidConfigException('Bale OTP client id is required.');
        }

        if ($this->clientSecret === '') {
            throw new InvalidConfigException('Bale OTP client secret is required.');
        }

        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidConfigException('Bale OTP base URL must be a valid URL.');
        }

        if ($this->timeoutSeconds < 1) {
            throw new InvalidConfigException('Bale OTP timeout must be at least one second.');
        }
    }
}
