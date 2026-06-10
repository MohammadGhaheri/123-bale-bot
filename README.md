# 123 Bale Bot

123 Bale Bot is a lightweight PHP SDK and small handler framework for building
bots for the Bale messenger Bot API. It is library-first: the reusable core is
independent from any CRM, loadboard, Laravel app, database, or route system.

The package follows the official Bale documentation where it is explicit, and
marks unclear behavior as needing verification instead of guessing from the
Telegram Bot API.

## What It Solves

- Send text and media messages through Bale.
- Receive updates through webhooks or long polling.
- Route commands, ordinary messages, and callback queries to dedicated handlers.
- Add middleware for webhook secrets or project-specific checks.
- Integrate with other PHP applications through small interfaces and adapters.

## Requirements

- PHP 8.1 or newer.
- HTTPS endpoint for production webhooks.
- A Bale bot token from `@botfather` inside Bale.

## Installation

```bash
composer require mohammadghaheri/bale-bot
```

During local development from this repository:

```bash
composer dump-autoload
cp .env.example .env
```

## Configuration

Never commit real credentials. Use environment variables or your host
application configuration:

```env
BALE_BOT_TOKEN=your-bale-bot-token
BALE_WEBHOOK_SECRET=your-random-secret
BALE_API_BASE_URL=https://tapi.bale.ai
APP_ENV=local
LOG_LEVEL=debug
```

## Quick Start

```php
<?php

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Support\BotConfig;

require __DIR__ . '/vendor/autoload.php';

$bot = new BaleClient(new BotConfig(getenv('BALE_BOT_TOKEN')));
$bot->sendMessage(123456789, 'Hello from 123 Bale Bot');
```

## Webhook

```php
$router = require __DIR__ . '/bot.php';
$handler = new OneTwoThree\BaleBot\Webhook\WebhookHandler($router, $bot);
$handler->handleJson(file_get_contents('php://input'), $_SERVER);
```

Set the webhook:

```php
$bot->setWebhook('https://example.com/bale/webhook');
```

Bale currently documents webhook support on ports `443` and `88`.

## Polling

```php
$poller = new OneTwoThree\BaleBot\Polling\Poller($bot, $router);
$poller->run();
```

## Handlers

```php
$router = new OneTwoThree\BaleBot\Router\Router();
$router->command('start', new StartCommandHandler());
$router->command('help', new HelpCommandHandler());
$router->message(new EchoHandler());
$router->fallback(new UnknownMessageHandler());
```

A handler only needs to implement `BotHandlerInterface`, so applications such
as Bijack or ELM Simple CRM can provide their own project handlers later
without this package depending on those projects.

## Adapters

Adapters live at application boundaries. The examples include dummy CRM and
loadboard adapters that show the shape of integration without connecting to any
real internal system.

## Security Notes

- Keep tokens in environment/config, never in code.
- Do not log tokens, secrets, credentials, real phone numbers, or private data.
- Use a secret path or internal header for webhooks when Bale does not provide
an official webhook signature for your setup.
- Validate and sanitize user input before storing or forwarding it.

## Current Limitations

- The client implements a reliable core plus generic `call()` access for other
documented Bale methods.
- Rate limits are not clearly documented in the official page reviewed on
2026-06-10, so retry behavior is conservative.
- Payments, chat administration, stickers, and mini-app details are documented
by Bale but are not wrapped with high-level domain objects yet.

## Bale vs Telegram

Bale states that its Bot API is based on Telegram Bot API with small changes.
Important Bale-specific notes include the `tapi.bale.ai` endpoint, Bale
`@botfather`, documented storage of the last 2000 updates for 24 hours, webhook
ports `443` and `88`, and methods such as `askReview`. See
[`docs/bale-api-notes.md`](docs/bale-api-notes.md).

## Roadmap

See [`docs/roadmap.md`](docs/roadmap.md).

## License

MIT.

## Creator

Built by Mohammad Ghaheri Najafabadi.

- YouTube: [@MohammadGhaheri](https://www.youtube.com/@MohammadGhaheri)
- LinkedIn: [mohammadghaheri](https://www.linkedin.com/in/mohammadghaheri)
- Email: [mohammad.ghaheri@gmail.com](mailto:mohammad.ghaheri@gmail.com)

## Open Source

123-bale-bot is released as open source software under the MIT license. See the
[LICENSE](LICENSE) file for details.
