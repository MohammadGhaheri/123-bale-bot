# ارسال OTP با Gateway بله

OTP بله جزو Bot API معمولی نیست. این سرویس از مسیر Gateway بله و بر اساس
شماره موبایل کاربر کار می‌کند.

منبع رسمی: <https://docs.bale.ai/gateway>

## دریافت دسترسی

برای استفاده از سرویس باید از درگاه بله اشتراک/دسترسی بگیرید و `client_id`
و `client_secret` دریافت کنید.

```env
BALE_OTP_CLIENT_ID=your-bale-otp-client-id
BALE_OTP_CLIENT_SECRET=your-bale-otp-client-secret
BALE_OTP_BASE_URL=https://safir.bale.ai/api/v2
BALE_OTP_SCOPE=read
```

## احراز هویت

Endpoint رسمی:

```text
POST https://safir.bale.ai/api/v2/auth/token
Content-Type: application/x-www-form-urlencoded
```

پارامترها:

- `grant_type`: مقدار ثابت `client_credentials`
- `client_secret`: رمز عبور کلاینت
- `scope`: مقدار `read`
- `client_id`: نام کاربری/شناسه کلاینت

## ارسال OTP

Endpoint رسمی:

```text
POST https://safir.bale.ai/api/v2/send_otp
Authorization: Bearer {access_token}
Content-Type: application/json
```

بدنه:

```json
{
  "phone": "989123456789",
  "otp": 123456
}
```

شماره باید در فرمت بین‌المللی ایران و بدون صفر ابتدایی باشد؛ مثل
`989123456789`. کد OTP باید عددی ۳ تا ۸ رقمی باشد.

## نمونه استفاده

```php
use OneTwoThree\BaleBot\Otp\OtpClient;
use OneTwoThree\BaleBot\Otp\OtpConfig;

$otp = new OtpClient(OtpConfig::fromEnv($_ENV + $_SERVER));

$result = $otp->sendOtp('09123456789', 123456);

echo 'Remaining balance: ' . ($result['balance'] ?? 'unknown');
```

## خطاهای مستندشده

- `400`: شماره تلفن نامعتبر
- `404`: کاربر با این شماره در بله حساب ندارد
- `402`: موجودی کافی نیست
- `500`: خطای داخلی سرور
- کد Gateway `18`: عبور از rate limit

## محدودیت نرخ

طبق مستندات رسمی Gateway:

- حداکثر ۳۰ درخواست در ساعت برای هر شماره تلفن
- حداکثر ۳۰۰ درخواست در دقیقه برای هر سازمان
- ارسال ناگهانی با burst بالا ممکن است رد شود
