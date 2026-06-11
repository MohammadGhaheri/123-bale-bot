<?php

declare(strict_types=1);

namespace OneTwoThree\BaleBot\Otp;

use OneTwoThree\BaleBot\Client\NativeHttpClient;
use OneTwoThree\BaleBot\Contracts\HttpClientInterface;
use OneTwoThree\BaleBot\Contracts\LoggerInterface;
use OneTwoThree\BaleBot\Exceptions\ApiException;
use OneTwoThree\BaleBot\Exceptions\InvalidConfigException;
use OneTwoThree\BaleBot\Support\NullLogger;
use OneTwoThree\BaleBot\Support\Redactor;

final class OtpClient
{
    private ?OtpToken $token = null;

    public function __construct(
        private readonly OtpConfig $config,
        private readonly ?HttpClientInterface $httpClient = null,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function authenticate(): OtpToken
    {
        $body = http_build_query([
            'grant_type' => 'client_credentials',
            'client_secret' => $this->config->clientSecret(),
            'scope' => $this->config->scope(),
            'client_id' => $this->config->clientId(),
        ]);

        $response = $this->request('POST', $this->config->url('auth/token'), [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ],
            'body' => $body,
        ]);

        if (!isset($response['access_token'])) {
            throw new ApiException('Bale OTP authentication response did not include an access token.');
        }

        $this->token = OtpToken::fromArray($response);
        return $this->token;
    }

    public function sendOtp(string $phone, int|string $otp, ?OtpToken $token = null): array
    {
        $phone = $this->normalizeIranianPhone($phone);
        $otp = (string) $otp;

        if (!preg_match('/^\d{3,8}$/', $otp)) {
            throw new InvalidConfigException('Bale OTP code must be a numeric value with 3 to 8 digits.');
        }

        $token ??= $this->token ?? $this->authenticate();

        return $this->request('POST', $this->config->url('send_otp'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token->accessToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode([
                'phone' => $phone,
                'otp' => (int) $otp,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);
    }

    public function normalizeIranianPhone(string $phone): string
    {
        $phone = preg_replace('/[^\d+]/', '', trim($phone)) ?? '';
        if (str_starts_with($phone, '+98')) {
            $phone = '98' . substr($phone, 3);
        } elseif (str_starts_with($phone, '09')) {
            $phone = '98' . substr($phone, 1);
        }

        if (!preg_match('/^989\d{9}$/', $phone)) {
            throw new InvalidConfigException('Bale OTP phone number must be an Iranian mobile number like 989123456789.');
        }

        return $phone;
    }

    private function request(string $method, string $url, array $options): array
    {
        $options['timeout'] = $this->config->timeoutSeconds();

        $this->log()->debug('Bale OTP request', [
            'method' => $method,
            'url' => $url,
            'headers' => $options['headers'] ?? [],
            'body' => $this->safeBodyForLog((string) ($options['body'] ?? '')),
        ]);

        $response = $this->client()->request($method, $url, $options);
        $decoded = json_decode($response['body'], true);

        if (!is_array($decoded)) {
            throw new ApiException('Bale OTP API returned a non-JSON response.');
        }

        $this->log()->debug('Bale OTP response', [
            'status' => $response['status'],
            'body' => Redactor::redact($decoded),
        ]);

        if ($response['status'] < 200 || $response['status'] >= 300) {
            throw new ApiException(
                (string) ($decoded['message'] ?? $decoded['error_description'] ?? 'Bale OTP API request failed.'),
                isset($decoded['code']) ? (int) $decoded['code'] : null,
                $decoded,
            );
        }

        return $decoded;
    }

    private function client(): HttpClientInterface
    {
        return $this->httpClient ?? new NativeHttpClient();
    }

    private function safeBodyForLog(string $body): mixed
    {
        $json = json_decode($body, true);
        if (is_array($json)) {
            return $json;
        }

        parse_str($body, $form);
        return $form !== [] ? $form : $body;
    }

    private function log(): LoggerInterface
    {
        return $this->logger ?? new NullLogger();
    }
}
