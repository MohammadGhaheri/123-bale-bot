# Security

- Never hardcode bot tokens.
- Never hardcode Gateway OTP `client_id`, `client_secret`, or access tokens.
- Commit `.env.example`, not `.env`.
- Redact tokens, secrets, credentials, phone numbers, and private database data
  from logs.
- Validate all user input before storing or forwarding it.
- Avoid returning raw exception messages to users.
- Use HTTPS for production webhooks.
- Use a secret webhook path or private header when official signature support is
  unavailable or unverified.
- Keep CRM/loadboard adapters abstract until each project provides its own
  audited integration.

Before publishing, run:

```bash
rg -n "token|password|secret|database_url|phone|otp|BALE_BOT_TOKEN|BALE_OTP" .
```

Review matches manually and confirm only placeholders or safe code remain.
