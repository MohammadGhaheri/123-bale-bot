<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/FakeHttpClient.php';

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Exceptions\ApiException;
use OneTwoThree\BaleBot\Handlers\EchoHandler;
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

$passed = 0;
foreach ($tests as $name => $test) {
    $test();
    $passed++;
    echo "[PASS] {$name}\n";
}

echo "{$passed} tests passed.\n";
