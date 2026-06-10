<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Handlers\EchoHandler;
use OneTwoThree\BaleBot\Handlers\HelpCommandHandler;
use OneTwoThree\BaleBot\Handlers\StartCommandHandler;
use OneTwoThree\BaleBot\Handlers\UnknownMessageHandler;
use OneTwoThree\BaleBot\Router\Router;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

return (new Router())
    ->command('start', new StartCommandHandler('Echo bot is ready. Send any text.'))
    ->command('help', new HelpCommandHandler("Send any text and I will echo it back."))
    ->message(new EchoHandler())
    ->fallback(new UnknownMessageHandler());
