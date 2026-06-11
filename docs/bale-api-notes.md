# Bale API Notes

Research date: 2026-06-10.

Primary source: the official Bale bot documentation at <https://docs.bale.ai/>.

## Technical Summary

Bale Bot API is documented as an HTTP API based on the Telegram Bot API with
small changes. Requests use:

```text
https://tapi.bale.ai/bot<token>/METHOD_NAME
```

Supported request encodings include URL query string,
`application/x-www-form-urlencoded`, `application/json` except for file upload,
and `multipart/form-data` for uploads. Responses are JSON objects with `ok`,
and on success usually contain `result`. Failed responses can include
`description`, `error_code`, and optional `parameters`.

## Bot Creation And Token

Create a bot by talking to `@botfather` in Bale. Bale issues a token shaped like
`123456789:secret`. Tokens must live outside source control.

## Receiving Updates

Supported:

- `getUpdates` for long polling.
- `setWebhook` to receive HTTPS POST updates.
- `deleteWebhook` to return to polling.
- `getWebhookInfo` to inspect webhook state.

Bale documents that updates are stored until delivery by polling or webhook;
currently the last 2000 messages are retained for 24 hours. `Update` may
contain at most one of the optional update payload types such as `message`,
`edited_message`, `callback_query`, or `pre_checkout_query`.

## Messaging And Interaction Support

Supported by official docs:

- Text messages with `sendMessage`.
- Reply keyboards and inline keyboards.
- Callback queries and `answerCallbackQuery`.
- File/media sending with `sendPhoto`, `sendAudio`, `sendDocument`,
  `sendVideo`, `sendAnimation`, `sendVoice`, and `sendMediaGroup`.
- File metadata/download flow with `getFile`.
- Contacts, locations, chat actions, message editing, and deletion.
- Chat administration methods.
- Stickers.
- Wallet/payment methods such as `sendInvoice`, `createInvoiceLink`,
  `answerPreCheckoutQuery`, and `inquireTransaction`.
- Bale-specific or less Telegram-common methods such as `askReview`.

## Confirmed Differences From Telegram

- Base URL is `https://tapi.bale.ai`, not Telegram's API host.
- Bot creation happens through Bale `@botfather`.
- Bale explicitly documents webhook ports `443` and `88`.
- Bale documents update retention as the last 2000 messages for 24 hours.
- Bale says method names are not case-sensitive.
- Some methods and payment concepts are Bale-specific or need separate testing.

## Unknown / Needs Verification

- Exact rate limits and retry-after behavior are not clearly documented on the
  reviewed official page.
- Official webhook signature or secret-header support was not confirmed on the
  reviewed page. This package therefore provides an internal secret middleware
  pattern that applications can enforce through a secret path or header.
- Multipart upload edge cases should be tested with real Bale bots before
  marking file upload support as fully complete.
- Payments and mini-app data are documented but need real sandbox testing
  before high-level wrappers are added.

## OTP Gateway

Bale OTP is not part of the regular Bot API. The official Gateway documentation
uses `https://safir.bale.ai/api/v2/auth/token` for client-credentials
authentication and `https://safir.bale.ai/api/v2/send_otp` for sending OTP
codes to Iranian mobile numbers. This package implements that flow in
`OneTwoThree\BaleBot\Otp\OtpClient`.

The documented phone format is `989123456789`, and OTP values must be numeric
with 3 to 8 digits. Gateway credentials must not be confused with bot tokens.

## Proposed Architecture

- `Client`: low-level API access, retries, response parsing, API exceptions.
- `Router`: routes commands, callbacks, messages, and fallback updates.
- `Handlers`: small project-specific units.
- `Middleware`: webhook secret checks and future cross-cutting behavior.
- `Contracts`: storage, logging, event dispatch, user resolving, handlers.
- `Storage`: memory, file, and SQLite examples.
- `Examples`: standalone echo/support bots and abstract CRM/loadboard adapters.
