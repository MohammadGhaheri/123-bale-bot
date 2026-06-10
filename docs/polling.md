# Polling

Use polling for local development, workers, or environments where inbound HTTPS
is not available.

```php
$poller = new Poller($bot, $router);
$poller->run();
```

`getUpdates` supports:

- `offset`: one greater than the last processed `update_id`.
- `limit`: 1 to 100.
- `timeout`: long polling wait time in seconds.

The `Poller` updates its offset after each processed update to avoid duplicate
handling.
