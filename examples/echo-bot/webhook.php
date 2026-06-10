<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Support\BotConfig;
use OneTwoThree\BaleBot\Webhook\WebhookHandler;

$router = require __DIR__ . '/bot.php';
$bot = new BaleClient(BotConfig::fromEnv($_ENV + $_SERVER));

(new WebhookHandler($router, $bot))->handleJson((string) file_get_contents('php://input'), $_SERVER);
