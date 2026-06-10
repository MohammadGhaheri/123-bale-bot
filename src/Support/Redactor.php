<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Support;

final class Redactor
{
    private const SENSITIVE_KEYS = [
        'token',
        'authorization',
        'secret',
        'password',
        'api_key',
        'database_url',
        'phone_number',
    ];

    public static function redact(mixed $value): mixed
    {
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $item) {
                $keyString = strtolower((string) $key);
                $clean[$key] = self::isSensitiveKey($keyString) ? '[redacted]' : self::redact($item);
            }

            return $clean;
        }

        if (is_string($value)) {
            return preg_replace('/bot[0-9]+:[A-Za-z0-9_\-]+/', 'bot[redacted]', $value) ?? $value;
        }

        return $value;
    }

    private static function isSensitiveKey(string $key): bool
    {
        foreach (self::SENSITIVE_KEYS as $sensitive) {
            if (str_contains($key, $sensitive)) {
                return true;
            }
        }

        return false;
    }
}
