<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/FakeHttpClient.php';

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Exceptions\ApiException;
use OneTwoThree\BaleBot\Handlers\EchoHandler;
use OneTwoThree\BaleBot\Otp\OtpClient;
use OneTwoThree\BaleBot\Otp\OtpConfig;
use OneTwoThree\BaleBot\Otp\OtpToken;
use OneTwoThree\BaleBot\Router\Router;
use OneTwoThree\BaleBot\Support\BotConfig;
use OneTwoThree\BaleBot\Support\NullLogger;
use OneTwoThree\BaleBot\Support\Update;
use OneTwoThree\BaleBot\Tests\FakeHttpClient;

function assert_true(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$tests = [];

$tests['client builds JSON request'] = function (): void {
    $http = new FakeHttpClient([
        ['status' => 200, 'headers' => [], 'body' => '{"ok":true,"result":{"message_id":1}}'],
    ]);
    $client = new BaleClient(new BotConfig('123:abc'), $http);
    $client->sendMessage(10, 'Hello');

    $request = $http->requests[0];
    assert_true($request['method'] === 'POST', 'Expected POST request.');
    assert_true(str_contains($request['url'], 'https://tapi.bale.ai/bot123:abc/sendMessage'), 'Unexpected request URL.');
    assert_true(json_decode($request['options']['body'], true)['text'] === 'Hello', 'Unexpected request body.');
};

$tests['config validates token'] = function (): void {
    try {
        new BotConfig('');
    } catch (Throwable) {
        assert_true(true, 'Invalid token rejected.');
        return;
    }

    throw new RuntimeException('Invalid token was accepted.');
};

$tests['router routes command'] = function (): void {
    $router = new Router();
    $handler = new EchoHandler();
    $router->command('start', $handler);
    $resolved = $router->resolve(Update::fromArray([
        'message' => ['text' => '/start', 'chat' => ['id' => 1]],
    ]));

    assert_true($resolved === $handler, 'Command did not resolve to expected handler.');
};

$tests['api errors throw exception'] = function (): void {
    $http = new FakeHttpClient([
        ['status' => 400, 'headers' => [], 'body' => '{"ok":false,"error_code":400,"description":"Bad Request"}'],
    ]);
    $client = new BaleClient(new BotConfig('123:abc'), $http);

    try {
        $client->getMe();
    } catch (ApiException $exception) {
        assert_true($exception->errorCode() === 400, 'Wrong API error code.');
        return;
    }

    throw new RuntimeException('API exception was not thrown.');
};

$tests['logger redacts token'] = function (): void {
    $http = new FakeHttpClient([
        ['status' => 200, 'headers' => [], 'body' => '{"ok":true,"result":{"message_id":2}}'],
    ]);
    $logger = new NullLogger();
    $client = new BaleClient(new BotConfig('123:abcSECRET'), $http, $logger);
    $client->sendMessage(10, 'Hello');

    $json = json_encode($logger->records);
    assert_true(!str_contains((string) $json, 'abcSECRET'), 'Token leaked in logs.');
};

$tests['otp authenticates with form data'] = function (): void {
    $http = new FakeHttpClient([
        ['status' => 200, 'headers' => [], 'body' => '{"access_token":"jwt-token","expires_in":43200,"scope":"read","token_type":"bearer"}'],
    ]);

    $client = new OtpClient(new OtpConfig('client-id', 'client-secret'), $http);
    $token = $client->authenticate();

    $request = $http->requests[0];
    parse_str($request['options']['body'], $body);

    assert_true($token->accessToken() === 'jwt-token', 'OTP token was not parsed.');
    assert_true(str_ends_with($request['url'], '/auth/token'), 'OTP auth URL is wrong.');
    assert_true($body['grant_type'] === 'client_credentials', 'OTP grant type is wrong.');
    assert_true($body['client_id'] === 'client-id', 'OTP client id is missing.');
    assert_true($body['client_secret'] === 'client-secret', 'OTP client secret is missing.');
};

$tests['otp sends normalized phone and code'] = function (): void {
    $http = new FakeHttpClient([
        ['status' => 200, 'headers' => [], 'body' => '{"balance":985}'],
    ]);

    $client = new OtpClient(new OtpConfig('client-id', 'client-secret'), $http);
    $result = $client->sendOtp('0912-345-6789', '123456', new OtpToken('jwt-token', 43200));

    $request = $http->requests[0];
    $body = json_decode($request['options']['body'], true);

    assert_true($result['balance'] === 985, 'OTP balance was not parsed.');
    assert_true(str_ends_with($request['url'], '/send_otp'), 'OTP send URL is wrong.');
    assert_true($request['options']['headers']['Authorization'] === 'Bearer jwt-token', 'OTP bearer token is missing.');
    assert_true($body['phone'] === '989123456789', 'OTP phone was not normalized.');
    assert_true($body['otp'] === 123456, 'OTP code was not sent as expected.');
};

$tests['otp logs redact phone and code'] = function (): void {
    $http = new FakeHttpClient([
        ['status' => 200, 'headers' => [], 'body' => '{"balance":985}'],
    ]);
    $logger = new NullLogger();

    $client = new OtpClient(new OtpConfig('client-id', 'client-secret'), $http, $logger);
    $client->sendOtp('09123456789', '654321', new OtpToken('jwt-secret', 43200));

    $json = json_encode($logger->records);
    assert_true(!str_contains((string) $json, '09123456789'), 'Raw local phone leaked in OTP logs.');
    assert_true(!str_contains((string) $json, '989123456789'), 'Normalized phone leaked in OTP logs.');
    assert_true(!str_contains((string) $json, '654321'), 'OTP code leaked in logs.');
    assert_true(!str_contains((string) $json, 'jwt-secret'), 'OTP access token leaked in logs.');
};

$passed = 0;
foreach ($tests as $name => $test) {
    $test();
    $passed++;
    echo "[PASS] {$name}\n";
}

echo "{$passed} tests passed.\n";
