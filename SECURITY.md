# Security Policy

Please do not publish tokens, webhook secrets, chat identifiers, user phone
numbers, database credentials, or production logs in issues or pull requests.

To report a vulnerability, open a private advisory when the repository is on
GitHub, or contact the maintainer through the future official security address.
Until then, use the placeholder address [security@example.com](mailto:security@example.com)
only in examples and replace it before public release.

Security fixes should avoid exposing sensitive request or response bodies. The
library redacts bot tokens, common secret fields, and authorization headers in
logs by default.
