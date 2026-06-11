# 123 Bale Bot

`123-bale-bot` یک کتابخانه و فریم‌ورک سبک PHP برای ساخت ربات پیام‌رسان بله است. هدف پروژه این است که توسعه‌دهندگان فارسی‌زبان بتوانند بدون وابستگی به یک پروژه خاص، یک هسته تمیز و قابل استفاده مجدد برای Bot API بله داشته باشند.

این پروژه فقط یک نمونه ربات ساده نیست؛ ساختار آن به شکل library-first طراحی شده تا بتوانید آن را در پروژه‌های مختلف PHP، سیستم‌های CRM، سامانه‌های پشتیبانی، سامانه‌های اعلام بار، یا پروژه‌های اختصاصی خودتان استفاده کنید.

## چرا این پروژه ساخته شده؟

پیام‌رسان بله یک پیام‌رسان ایرانی است و بسیاری از کسب‌وکارها و توسعه‌دهندگان فارسی‌زبان به ابزارهای ساده، خوانا و قابل توسعه برای ساخت ربات‌های بله نیاز دارند. `123-bale-bot` تلاش می‌کند یک نقطه شروع استاندارد برای این نیاز باشد:

- ارسال پیام متنی و رسانه‌ای در بله
- دریافت پیام‌های کاربران با Webhook یا Polling
- مدیریت دستورهایی مثل `/start` و `/help`
- route کردن پیام‌ها، commandها و callbackها به handler مناسب
- امکان ساخت adapter برای اتصال به پروژه‌های دیگر
- رعایت اصول امنیتی برای token، webhook و logها
- ساختار مناسب برای انتشار عمومی در GitHub

## قابلیت‌ها

- کلاینت سبک برای Bot API بله
- پشتیبانی از `sendMessage`
- پشتیبانی اولیه از ارسال عکس و فایل
- دریافت updateها با `getUpdates`
- تنظیم و حذف webhook
- سیستم router برای command، message، callback و fallback
- کلاینت جداگانه برای ارسال OTP از طریق Gateway بله
- handlerهای آماده برای شروع، راهنما، echo و پیام ناشناخته
- middleware برای بررسی secret داخلی webhook
- storageهای نمونه شامل memory، file و SQLite
- adapterهای نمونه برای CRM و سامانه اعلام بار
- تست‌های پایه بدون نیاز به وابستگی سنگین
- مستندات داخلی درباره تفاوت‌های بله و Telegram Bot API

## نیازمندی‌ها

- PHP نسخه 8.1 یا بالاتر
- Composer
- توکن ربات بله از `@botfather` داخل پیام‌رسان بله
- آدرس HTTPS برای استفاده production از webhook

## نصب

بعد از انتشار پکیج در Packagist:

```bash
composer require mohammadghaheri/bale-bot
```

برای توسعه مستقیم از همین repository:

```bash
composer dump-autoload
cp .env.example .env
```

## تنظیمات

توکن واقعی را هیچ‌وقت داخل کد یا repository قرار ندهید. تنظیمات را از `.env` یا config پروژه میزبان بخوانید.

```env
BALE_BOT_TOKEN=your-bale-bot-token
BALE_WEBHOOK_SECRET=your-random-secret
BALE_API_BASE_URL=https://tapi.bale.ai
APP_ENV=local
LOG_LEVEL=debug
BALE_OTP_CLIENT_ID=your-bale-otp-client-id
BALE_OTP_CLIENT_SECRET=your-bale-otp-client-secret
BALE_OTP_BASE_URL=https://safir.bale.ai/api/v2
BALE_OTP_SCOPE=read
```

فایل `.env.example` فقط مقدارهای نمونه دارد. فایل `.env` واقعی نباید commit شود.

## شروع سریع

```php
<?php

use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Support\BotConfig;

require __DIR__ . '/vendor/autoload.php';

$bot = new BaleClient(new BotConfig(getenv('BALE_BOT_TOKEN')));

$bot->sendMessage(123456789, 'سلام از 123 Bale Bot');
```

## استفاده با Webhook

یک handler ساده برای endpoint وب‌هوک:

```php
$router = require __DIR__ . '/bot.php';

$handler = new OneTwoThree\BaleBot\Webhook\WebhookHandler($router, $bot);
$handler->handleJson(file_get_contents('php://input'), $_SERVER);
```

تنظیم webhook:

```php
$bot->setWebhook('https://example.com/bale/webhook');
```

حذف webhook:

```php
$bot->deleteWebhook();
```

طبق مستندات رسمی بله، webhook روی پورت‌های `443` و `88` پشتیبانی می‌شود.

## استفاده با Polling

برای محیط توسعه یا زمانی که endpoint HTTPS ندارید، می‌توانید از polling استفاده کنید:

```php
$poller = new OneTwoThree\BaleBot\Polling\Poller($bot, $router);
$poller->run();
```

## ارسال OTP با Gateway بله

ارسال OTP جزو Bot API معمولی نیست و از طریق Gateway بله انجام می‌شود. برای
استفاده باید از درگاه/سامانه بله دسترسی گرفته باشید و `client_id` و
`client_secret` داشته باشید.

```php
<?php

use OneTwoThree\BaleBot\Otp\OtpClient;
use OneTwoThree\BaleBot\Otp\OtpConfig;

require __DIR__ . '/vendor/autoload.php';

$otp = new OtpClient(OtpConfig::fromEnv($_ENV + $_SERVER));

$result = $otp->sendOtp('09123456789', 123456);
```

این کلاینت شماره `09123456789` را به فرمت رسمی `989123456789` تبدیل می‌کند.
کد OTP باید عددی ۳ تا ۸ رقمی باشد. جزئیات بیشتر در
[`docs/otp.md`](docs/otp.md) آمده است.

## ساخت Router و Handler

```php
$router = new OneTwoThree\BaleBot\Router\Router();

$router->command('start', new StartCommandHandler());
$router->command('help', new HelpCommandHandler());
$router->message(new EchoHandler());
$router->fallback(new UnknownMessageHandler());
```

هر handler فقط باید `BotHandlerInterface` را پیاده‌سازی کند:

```php
use OneTwoThree\BaleBot\Client\BaleClient;
use OneTwoThree\BaleBot\Contracts\BotHandlerInterface;
use OneTwoThree\BaleBot\Support\Update;

final class SupportTicketHandler implements BotHandlerInterface
{
    public function handle(Update $update, BaleClient $client): void
    {
        $chatId = $update->chatId();
        $text = $update->text();

        if ($chatId !== null && $text !== null) {
            $client->sendMessage($chatId, 'درخواست پشتیبانی شما ثبت شد.');
        }
    }
}
```

## اتصال به پروژه‌های دیگر

این پکیج به هیچ پروژه داخلی یا خارجی خاصی وابسته نیست. برای اتصال به سیستم‌هایی مثل CRM، سامانه پشتیبانی یا سامانه اعلام بار باید adapter اختصاصی همان پروژه را بسازید.

نمونه‌های انتزاعی داخل repository:

- `examples/crm-adapter-example`
- `examples/loadboard-adapter-example`

این نمونه‌ها فقط شکل صحیح اتصال را نشان می‌دهند و به هیچ دیتابیس یا پروژه واقعی وصل نیستند.

## مثال‌های آماده

- `examples/echo-bot`: رباتی که پیام کاربر را برمی‌گرداند
- `examples/support-bot`: نمونه ساده ثبت درخواست پشتیبانی
- `examples/crm-adapter-example`: نمونه adapter برای CRM
- `examples/loadboard-adapter-example`: نمونه adapter برای سامانه اعلام بار

## نکات امنیتی

- توکن ربات را داخل کد hardcode نکنید.
- فایل `.env` واقعی را commit نکنید.
- token، secret، اطلاعات دیتابیس، شماره موبایل واقعی و اطلاعات خصوصی را log نکنید.
- ورودی کاربر را قبل از ذخیره یا ارسال به سیستم‌های دیگر validate و sanitize کنید.
- خطاهای حساس را مستقیم به کاربر نمایش ندهید.
- برای webhook از HTTPS استفاده کنید.
- اگر signature رسمی برای webhook در دسترس نبود یا نیاز به بررسی داشت، از secret path یا header داخلی استفاده کنید.

## تفاوت‌های مهم بله با Telegram Bot API

بله در مستندات خود توضیح داده که Bot API آن بر پایه Telegram Bot API طراحی شده، اما نباید فرض کنیم همه رفتارها دقیقاً مشابه Telegram است.

موارد مهم:

- آدرس API بله: `https://tapi.bale.ai/bot<token>/METHOD_NAME`
- ساخت ربات از طریق `@botfather` در خود بله انجام می‌شود.
- بله نگهداری آخرین ۲۰۰۰ پیام تا ۲۴ ساعت را برای updateها مستند کرده است.
- webhook طبق مستندات روی پورت‌های `443` و `88` پشتیبانی می‌شود.
- برخی امکانات مثل پرداخت، mini app، فایل‌ها و متدهای خاص بله نیاز به تست واقعی بیشتری دارند.
- OTP با شماره موبایل از طریق Gateway/Safir بله انجام می‌شود و بخشی از Bot API معمولی نیست.

جزئیات بیشتر در فایل [`docs/bale-api-notes.md`](docs/bale-api-notes.md) آمده است.

## محدودیت‌های فعلی

- client هسته اصلی و متد عمومی `call()` را فراهم می‌کند، اما برای همه متدهای مستندشده بله wrapper سطح بالا نوشته نشده است.
- rate limitهای دقیق در مستندات بررسی‌شده به شکل کامل مشخص نبودند.
- پرداخت، مدیریت پیشرفته چت، stickerها و mini appها قبل از تولید wrapper کامل نیاز به تست واقعی دارند.
- upload چندبخشی فایل‌ها در نسخه‌های بعدی باید کامل‌تر و عملی‌تر شود.
- ارسال OTP اضافه شده، اما نیازمند credential واقعی Gateway بله و موجودی/اشتراک فعال است.

## اجرای تست‌ها

```bash
composer test
composer lint
```

## نقشه راه

نقشه راه پروژه در [`docs/roadmap.md`](docs/roadmap.md) قرار دارد.

## سازنده

ساخته شده توسط محمد قاهری نجف‌آبادی.

- YouTube: [@MohammadGhaheri](https://www.youtube.com/@MohammadGhaheri)
- LinkedIn: [mohammadghaheri](https://www.linkedin.com/in/mohammadghaheri)
- Email: [mohammad.ghaheri@gmail.com](mailto:mohammad.ghaheri@gmail.com)

## متن‌باز بودن

`123-bale-bot` به صورت متن‌باز و با مجوز MIT منتشر شده است. متن کامل مجوز را می‌توانید در فایل [LICENSE](LICENSE) ببینید.
