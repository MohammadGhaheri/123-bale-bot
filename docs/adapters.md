# Adapters

Adapters belong at project boundaries. This package defines interfaces and
example implementations, but does not connect to real Bijack, ELM Simple CRM,
or any production database.

Typical application flow:

1. Your project implements an adapter interface.
2. A bot handler receives a Bale update.
3. The handler validates/sanitizes the user text.
4. The handler calls the adapter.
5. The handler replies to the user with a safe status message.

See:

- `examples/crm-adapter-example`
- `examples/loadboard-adapter-example`
