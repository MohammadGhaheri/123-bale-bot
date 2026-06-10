<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Polling\Poller;
use OneTwoThree\BaleBot\Support\BotConfig;

$router = require __DIR__ . '/bot.php';
$bot = new BaleClient(BotConfig::fromEnv($_ENV + $_SERVER));

(new Poller($bot, $router))->run();
