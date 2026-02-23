# MASTER BUILD PROMPT — Rebuild TruWhatsApp with Shopify CLI + TypeScript (Built for Shopify Oriented)

Use this exact prompt with a coding agent.

## Empty Project Usage (Important)

This prompt is designed to be used in a **blank directory**.

Before running it, ensure these are installed on your machine:
- Node.js LTS (v20+ recommended)
- npm (or pnpm/yarn)
- Shopify CLI (latest)
- Docker (recommended for local Postgres/Redis) or locally installed Postgres + Redis
- Git

When giving this prompt to an agent, add this one-line preface:

"You are starting in an empty folder. Initialize everything from scratch with Shopify CLI and implement all requirements below."

If the agent asks for startup commands, use this baseline:

```bash
shopify app init
# choose: Remix app (TypeScript), embedded app

# then inside generated app folder
npm install

# add required deps
npm install @prisma/client prisma zod bullmq ioredis pino pino-pretty
npm install -D @playwright/test vitest

# initialize prisma
npx prisma init
```

Use a `.env.example` with placeholders only (no real secrets).

---

You are a senior Shopify app engineer. Build a production-ready Shopify embedded app in **TypeScript** using **Shopify CLI** that recreates the current TruWhatsApp app behavior, but with modern architecture and quality standards aimed at **Built for Shopify eligibility**.

## 0) Objective

Rebuild the existing PHP app as a new Shopify app with the same business functionality:

1. Merchant installs app and authenticates with Shopify OAuth.
2. Merchant connects WhatsApp session by scanning QR (via waapi.app instance).
3. App sends automated WhatsApp messages for:
   - New order
   - Abandoned checkout
   - Fulfillment / shipment
4. Merchant can edit 3 templates with dynamic placeholders.
5. App has recurring billing gate.
6. App cleans up on uninstall.

Do not migrate PHP code directly. Re-implement with Shopify-first TypeScript architecture.

---

## 1) Tech Stack (Required)

- Shopify CLI app scaffold (latest stable)
- Node.js + TypeScript
- Shopify embedded app framework from CLI template (Remix-based current default is acceptable)
- Shopify App Bridge + Polaris (latest stable)
- Prisma ORM + PostgreSQL (SQLite only for local dev if template enforces; production must be Postgres)
- Queue/job worker for outbound message sending (BullMQ + Redis or equivalent)
- Zod for API validation
- Vitest/Jest + Playwright for tests
- ESLint + Prettier

If Shopify CLI template changes, use the latest official embedded TypeScript template and adapt.

---

## 2) Built for Shopify-Oriented Constraints (Critical)

Implement with these constraints in mind:

1. Embedded-first UX with App Bridge and Polaris patterns.
2. Fast, reliable, and non-blocking backend processing (queue for sends).
3. Secure-by-default:
   - Verify Shopify webhook HMAC
   - Validate all inbound payloads
   - Encrypt secrets at rest where applicable
   - No hardcoded credentials
4. Privacy and compliance:
   - Implement GDPR webhooks (customers/data_request, customers/redact, shop/redact)
   - Data minimization and retention policy
5. Billing through Shopify-managed billing APIs (GraphQL app subscription), not custom hacks.
6. Proper uninstall cleanup and idempotent webhooks.
7. Good operational quality:
   - structured logs
   - error boundaries
   - health endpoint
   - retry policies with dead-letter handling
8. Remove non-essential tracking scripts (no Clarity/Meta pixel unless explicitly re-approved).

Do not claim “Built for Shopify approved”; implement to maximize eligibility.

---

## 3) Functional Parity Requirements

## 3.1 WhatsApp instance lifecycle (waapi.app)

Provide service module for waapi with methods equivalent to:
- create instance
- fetch status
- fetch QR
- reboot
- send message
- logout (optional)
- delete instance

Persist instance mapping per shop.

## 3.2 Event triggers

Replace polling cron architecture with webhook-driven event ingestion where possible:
- Orders create/update webhook for new order notification logic
- Checkouts/abandoned checkouts trigger path (if direct webhook unavailable, use scheduled job with checkpointing)
- Fulfillment events via fulfillment webhooks

Design for idempotency (event dedupe key table).

## 3.3 Message template editor

UI must support 3 editable templates:
- New order template
- Abandoned checkout template
- Fulfillment template

Dynamic placeholders must include:
- {{customer_name}}
- {{shipping_phone}}
- {{customer_phone}}
- {{customer_email}}
- {{shipping_address}}
- {{billing_address}}
- {{line_items}}
- {{subtotal}}
- {{subtotal_with_currency}}
- {{total}}
- {{total_with_currency}}
- {{order_no}}
- {{checkout_link}}
- {{fulfilled_by}}
- {{tracking_id}}
- {{tracking_link}}

Include helper UI for inserting placeholders and character limits.

## 3.4 Billing

- Implement recurring subscription plans via Shopify GraphQL billing API.
- Enforce active subscription before message automation runs.
- Include plan selection/confirmation UX in-app.

## 3.5 Uninstall + data lifecycle

On app uninstall:
- Mark shop inactive
- Revoke/delete waapi instance
- Remove or anonymize shop data per retention policy

Implement configurable log retention (e.g., success 24h, errors 1h) as a policy module.

---

## 4) Data Model (Target)

Use Prisma schema with at minimum:

1. `Shop`
   - id
   - shopDomain (unique)
   - accessToken (encrypted)
   - waapiInstanceId
   - isActive
   - billingStatus
   - createdAt, updatedAt

2. `MessageTemplate`
   - id
   - shopId (FK)
   - type (NEW_ORDER | ABANDONED | FULFILLMENT)
   - content
   - updatedAt

3. `MessageLog`
   - id
   - shopId
   - receiver
   - payloadType
   - status (SUCCESS | ERROR)
   - message
   - externalMessageId nullable
   - createdAt

4. `EventCheckpoint` (if polling fallback is needed)
   - id
   - shopId
   - streamType
   - lastExternalId
   - updatedAt

5. `WebhookEvent`
   - id
   - shopId
   - topic
   - externalEventId (unique per topic+shop)
   - processedAt

6. `AppInstallation`
   - id
   - shopId
   - installedAt
   - uninstalledAt nullable

Add indexes for high-frequency lookup paths.

---

## 5) App Modules / Folder Design

Create clean modular architecture:

- `app/routes/*` (embedded UI pages)
- `app/components/*` (Polaris components)
- `app/services/shopify.server.ts`
- `app/services/waapi.server.ts`
- `app/services/template-renderer.server.ts`
- `app/services/phone-normalization.server.ts`
- `app/services/billing.server.ts`
- `app/services/webhook-handlers/*`
- `app/jobs/*` (queue producers/consumers)
- `app/db/*` (Prisma client, repositories)
- `app/lib/*` (validation, crypto, logger)

Provide strong typing and avoid `any`.

---

## 6) API/Route Requirements

Implement internal authenticated endpoints (embedded app session-based) for:
- get waapi status
- get QR
- reboot instance
- update templates
- test-send message (optional admin utility)

Also implement webhook routes for:
- app/uninstalled
- orders/create (or chosen order topic)
- fulfillment events
- GDPR webhooks

All routes must:
- validate input with Zod
- return typed JSON responses
- include robust error handling

---

## 7) UX Requirements

Build a minimal but polished embedded admin UI with Polaris:

1. Connection status card
   - status badge
   - QR display panel
   - reboot button

2. Template management tabs
   - New Order
   - Abandoned Checkout
   - Fulfillment
   - placeholder inserter
   - save/update action with toast feedback

3. Billing status/plan page

4. Privacy + data handling page

Use accessible Polaris components and responsive layout.

---

## 8) Migration Mapping from Legacy App

Map legacy behavior to new implementation:

- Legacy `Activity::process_payload` -> `template-renderer + queue send job`
- Legacy cron loops -> webhook-driven jobs + optional scheduled reconciliation worker
- Legacy `.tokens/` files -> DB session/token storage
- Legacy raw SQL -> Prisma repositories
- Legacy jQuery polling -> typed loader/action endpoints + modern React state updates

Document any intentional behavior changes.

---

## 9) Security / Reliability Requirements

1. Secrets in env vars only.
2. HMAC verification for all webhooks.
3. Retry/backoff for waapi transient failures.
4. Circuit-breaker style protection for repeated waapi outages.
5. Idempotent webhook processing.
6. Rate-limit outbound sends per shop.
7. Audit logging with PII-safe formatting.

---

## 10) Deliverables (Mandatory)

Provide all of the following:

1. Complete source code scaffolded via Shopify CLI.
2. Prisma schema + migrations.
3. `.env.example` with required vars.
4. `README.md` with:
   - setup
   - local run
   - webhook testing
   - billing setup
   - deployment notes
5. `ARCHITECTURE.md` with sequence diagrams or clear flow descriptions.
6. `MIGRATION_NOTES.md` describing parity and differences from legacy PHP app.
7. Seed script for dev templates.
8. Test suite (unit + minimal integration/e2e).

---

## 11) Acceptance Criteria

The build is accepted only if:

1. App installs and loads embedded in Shopify admin.
2. waapi instance can be created and QR displayed.
3. Merchant can update all 3 templates and persist changes.
4. At least one real webhook path triggers queued message send workflow.
5. Billing gate blocks non-subscribed shops from automation.
6. Uninstall webhook performs cleanup correctly.
7. GDPR webhook endpoints exist and return compliant acknowledgements.
8. TypeScript build passes with no type errors.
9. Lint and tests pass.

---

## 12) Build Plan (Execute in order)

1. Scaffold app with Shopify CLI (TypeScript embedded template).
2. Set up Prisma + DB models.
3. Build auth/session foundation from template.
4. Implement waapi service module.
5. Implement UI screens and template CRUD.
6. Implement billing service and plan gate.
7. Implement webhook ingestion + idempotency.
8. Implement queue worker for send jobs.
9. Implement uninstall/GDPR handlers.
10. Add tests, docs, and polish.

---

## 13) Coding Rules

- Prefer small cohesive modules.
- No dead code, no mock placeholders in final deliverable.
- Strong runtime validation at boundaries.
- Use async/await and explicit error mapping.
- Keep naming explicit and consistent.

---

## 14) Output Format Required from Agent

Return:
1. A concise summary of what was built.
2. Folder tree of key files.
3. Important code snippets for critical flows (webhook -> queue -> waapi send).
4. Commands to run locally.
5. Known limitations and next steps.

---

End of prompt.