# Webhook Usage

Bale sends updates as HTTPS POST requests containing JSON.

```php
$bot = new BaleClient(BotConfig::fromEnv($_ENV));
$router = require __DIR__ . '/bot.php';
$webhook = new WebhookHandler($router, $bot);
$webhook->handleJson(file_get_contents('php://input'), $_SERVER);
```

Set a webhook:

```php
$bot->setWebhook('https://example.com/bale/webhook');
```

Delete it:

```php
$bot->deleteWebhook();
```

The official docs currently mention webhook ports `443` and `88`.

## Internal Secret Pattern

If your deployment does not have an official Bale signature, use a hard-to-guess
path or require a private header at your web server/application boundary, then
pass that value to `WebhookSecretMiddleware`.
