# Handlers

Handlers implement:

```php
interface BotHandlerInterface
{
    public function handle(Update $update, BaleClient $client): void;
}
```

Register command handlers:

```php
$router->command('start', new StartCommandHandler());
$router->command('help', new HelpCommandHandler());
```

Register callback handlers by data prefix:

```php
$router->callback('ticket:', new SupportTicketHandler($storage));
```

Register a general message handler and fallback:

```php
$router->message(new EchoHandler());
$router->fallback(new UnknownMessageHandler());
```

This shape lets projects provide handlers such as `BijackBotHandler`,
`ElmCrmBotHandler`, `SupportTicketHandler`, or `LoadNotificationHandler`
without coupling this package to those applications.
