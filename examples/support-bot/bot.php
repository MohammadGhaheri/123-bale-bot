<?php

declare(strict_types=1);

use OneTwoThree\BaleBot\Handlers\HelpCommandHandler;
use OneTwoThree\BaleBot\Handlers\StartCommandHandler;
use OneTwoThree\BaleBot\Router\Router;
use OneTwoThree\BaleBot\Storage\FileStorage;

require dirname(__DIR__, 2) . '/vendor/autoload.php';
require __DIR__ . '/SupportTicketHandler.php';

$storage = new FileStorage(__DIR__ . '/tickets.json');

return (new Router())
    ->command('start', new StartCommandHandler('Welcome. Send your support request as a message.'))
    ->command('help', new HelpCommandHandler('Send one message describing your support request.'))
    ->message(new SupportTicketHandler($storage));
