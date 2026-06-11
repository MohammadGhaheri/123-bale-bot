<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Otp;

final class OtpToken
{
    public function __construct(
        private readonly string $accessToken,
        private readonly int $expiresIn,
        private readonly string $tokenType = 'bearer',
        private readonly string $scope = 'read',
    ) {
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            (string) ($payload['access_token'] ?? ''),
            (int) ($payload['expires_in'] ?? 0),
            (string) ($payload['token_type'] ?? 'bearer'),
            (string) ($payload['scope'] ?? 'read'),
        );
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }

    public function expiresIn(): int
    {
        return $this->expiresIn;
    }

    public function tokenType(): string
    {
        return $this->tokenType;
    }

    public function scope(): string
    {
        return $this->scope;
    }
}
