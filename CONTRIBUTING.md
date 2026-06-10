# Contributing

Thanks for helping improve 123 Bale Bot.

## Setup

```bash
composer install
cp .env.example .env
```

## Tests

```bash
composer test
composer lint
```

The current test runner is intentionally dependency-free so the package can be
validated before dev dependencies are installed.

## Issues

When opening an issue, include the PHP version, Bale API method, expected
behavior, actual behavior, and a sanitized payload if possible.

## Pull Requests

Keep pull requests focused. Do not include real tokens, production database
details, private project paths, phone numbers, or internal CRM/loadboard data.
