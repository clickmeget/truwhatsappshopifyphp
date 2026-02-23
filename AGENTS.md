# AGENTS.md — Execution Guide for AI Coding Agents

## Mission
Build a production-ready Shopify embedded app in TypeScript that reproduces TruWhatsApp business behavior with modern architecture and Built for Shopify-oriented engineering quality.

## Start Condition
Assume this repository may start empty. If empty:
1. Initialize with Shopify CLI official embedded TypeScript template.
2. Install required dependencies.
3. Set up Prisma, database, queue, and baseline docs.

## Recommended Bootstrap (if needed)
```bash
shopify app init
# choose embedded TypeScript template
npm install
npm install @prisma/client prisma zod bullmq ioredis pino pino-pretty
npm install -D vitest @playwright/test
npx prisma init
```

## Non-Negotiable Requirements
1. TypeScript-first, strict typing, no unnecessary `any`.
2. Webhook HMAC verification and runtime payload validation.
3. Queue-based outbound sending; no synchronous long-running send flow.
4. Idempotent webhook/event processing.
5. Shopify-managed billing using GraphQL subscriptions.
6. GDPR and uninstall handlers implemented.
7. No hardcoded credentials; env-driven config.

## Required Functional Domains
- **Shop auth/install**: Shopify OAuth/session handling.
- **waapi integration**: create instance, status, QR, reboot, send, delete.
- **Template system**: 3 templates + placeholder rendering.
- **Event handling**: webhook-first notifications for order/fulfillment, fallback reconciliation only if necessary.
- **Billing gate**: block automation when unsubscribed.
- **Data lifecycle**: cleanup and retention policies.

## Placeholder Contract
Support exactly:
- `{{customer_name}}`
- `{{shipping_phone}}`
- `{{customer_phone}}`
- `{{customer_email}}`
- `{{shipping_address}}`
- `{{billing_address}}`
- `{{line_items}}`
- `{{subtotal}}`
- `{{subtotal_with_currency}}`
- `{{total}}`
- `{{total_with_currency}}`
- `{{order_no}}`
- `{{checkout_link}}`
- `{{fulfilled_by}}`
- `{{tracking_id}}`
- `{{tracking_link}}`

## Minimum Data Models
Implement Prisma models equivalent to:
- `Shop`
- `MessageTemplate`
- `MessageLog`
- `WebhookEvent`
- `AppInstallation`
- `EventCheckpoint` (only if polling fallback exists)

## Suggested Module Layout
- `app/routes/*`
- `app/components/*`
- `app/services/shopify.server.ts`
- `app/services/waapi.server.ts`
- `app/services/template-renderer.server.ts`
- `app/services/phone-normalization.server.ts`
- `app/services/billing.server.ts`
- `app/services/webhook-handlers/*`
- `app/jobs/*`
- `app/db/*`
- `app/lib/*`

## Quality Bar
- Structured logs and clear error boundaries
- Retry/backoff and dead-letter strategy for send failures
- Rate limiting per shop for outbound sends
- Accessible Polaris UI and embedded app UX conventions

## Required Output Artifacts
- source code
- Prisma schema + migrations
- `.env.example`
- `README.md`
- `ARCHITECTURE.md`
- `MIGRATION_NOTES.md`
- seed script
- tests (unit + minimal integration/e2e)

## Acceptance Checklist
- Embedded app installs and loads in Shopify Admin
- waapi connection + QR flow works
- template CRUD works for all 3 template types
- webhook-to-queue-to-send path verified
- billing enforcement works
- uninstall/GDPR endpoints implemented
- typecheck, lint, and tests pass
