# Installation

Install with Composer once the package is published:

```bash
composer require mohammadghaheri/bale-bot
```

For local development:

```bash
composer dump-autoload
cp .env.example .env
```

Set `BALE_BOT_TOKEN` from Bale `@botfather`. Do not commit `.env`.

For OTP Gateway usage, also set `BALE_OTP_CLIENT_ID` and
`BALE_OTP_CLIENT_SECRET`. These are Gateway credentials from Bale/Safir and are
not the same as the bot token.

The suggested Composer package name, `mohammadghaheri/bale-bot`, is valid for
Composer because it uses the required `vendor/package` format with lowercase
letters and hyphens. The PHP namespace is `OneTwoThree\BaleBot`.
