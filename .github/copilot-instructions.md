# Copilot Instructions — Shopify CLI + TypeScript Rebuild

## Project Goal
Rebuild the legacy TruWhatsApp PHP app as a modern Shopify embedded app using Shopify CLI + TypeScript, targeting Built for Shopify-oriented quality.

## Ground Rules
- Start from an empty folder and scaffold with official Shopify CLI template.
- Use TypeScript everywhere; avoid `any`.
- Do not port legacy PHP code line-by-line; re-implement behavior cleanly.
- Keep modules small, cohesive, and strongly typed.
- Validate all external input with Zod.
- Use async/await with explicit error mapping.

## Required Stack
- Shopify CLI embedded app template (TypeScript, Remix-based if current default)
- Shopify App Bridge + Polaris
- Prisma + PostgreSQL (SQLite only for local template defaults)
- Queue worker for outbound sends (BullMQ + Redis or equivalent)
- Zod, ESLint, Prettier, Vitest/Jest, Playwright

## Core Functional Requirements
Implement parity for:
1. Shopify OAuth install/auth
2. waapi instance lifecycle (create/status/QR/reboot/send/delete)
3. Template editor for 3 message types:
   - NEW_ORDER
   - ABANDONED
   - FULFILLMENT
4. Placeholder insertion support:
   - `{{customer_name}}`, `{{shipping_phone}}`, `{{customer_phone}}`, `{{customer_email}}`
   - `{{shipping_address}}`, `{{billing_address}}`, `{{line_items}}`
   - `{{subtotal}}`, `{{subtotal_with_currency}}`, `{{total}}`, `{{total_with_currency}}`
   - `{{order_no}}`, `{{checkout_link}}`, `{{fulfilled_by}}`, `{{tracking_id}}`, `{{tracking_link}}`
5. Billing gate via Shopify GraphQL app subscription APIs
6. Uninstall cleanup + GDPR endpoints

## Event Processing Requirements
- Prefer webhook-driven processing over cron polling.
- Handle:
  - `app/uninstalled`
  - order/fulfillment topics needed for notifications
  - GDPR topics (`customers/data_request`, `customers/redact`, `shop/redact`)
- Enforce idempotency on webhook/event handling.
- Queue outbound WhatsApp sends; avoid blocking request threads.

## Security & Compliance
- Verify Shopify webhook HMAC on every webhook request.
- Store secrets only in environment variables.
- No hardcoded keys/tokens.
- Encrypt sensitive tokens at rest.
- Implement retention policy for logs (e.g., success 24h, error 1h configurable).
- Use PII-safe structured logging.

## Data Model Minimum
Use Prisma models equivalent to:
- `Shop`
- `MessageTemplate`
- `MessageLog`
- `WebhookEvent` (dedupe)
- `EventCheckpoint` (only if polling fallback used)
- `AppInstallation`

Add practical indexes for frequent reads/writes.

## UI Expectations
Build embedded Polaris UI with:
- Connection/status card + QR panel + reboot action
- Template management tabs for 3 event types
- Placeholder inserter + save feedback
- Billing status/plan page
- Privacy/data-handling page

## Mandatory Deliverables
- Working source code scaffolded with Shopify CLI
- Prisma schema + migrations
- `.env.example`
- `README.md` (setup, run, webhook, billing, deploy)
- `ARCHITECTURE.md`
- `MIGRATION_NOTES.md`
- seed script for default templates
- tests (unit + minimal integration/e2e)

## Definition of Done
- Embedded install/auth works
- waapi QR/status flow works
- 3 template CRUD works
- At least one real webhook path triggers queue-based send
- Billing gate enforced
- Uninstall + GDPR handlers implemented
- Typecheck/lint/tests pass
