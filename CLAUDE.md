# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

Laravel 12 admin dashboard for managing a TikTok affiliate program: creators (kreator), stores (toko), products, and sample-shipping workflows (kirim sampel) with follow-up tracking. Domain terms are kept in Indonesian throughout routes, view vars, and UI text — do not translate them.

No frontend SPA/API layer — this is a server-rendered Blade app with jQuery/Select2-style AJAX endpoints for autocomplete search. Auth is session-based (`Auth` facade), single `auth` middleware group, role-gated (`super admin` vs regular) rather than policy/gate-based.

## Commands

```bash
composer dev              # runs php artisan serve + queue:listen + vite concurrently — normal local dev entrypoint
php artisan serve
npm run dev                # vite only
npm run build

composer test              # config:clear then php artisan test (Pest)
php artisan test --filter=TestName
php artisan test tests/Feature/SomeTest.php

vendor\bin\pint            # code style (Laravel Pint)
```

Database: MySQL/MariaDB, database name `affiliator_db`. Only a handful of migrations exist in `database/migrations` (users/cache/jobs + one indexes migration) — most tables (`stores`, `categories`, `products`, `send_sample`, `contentstyles`, pivot tables, etc.) predate the migration history in this repo, so **don't assume `php artisan migrate:fresh` reproduces the schema**. Check the live DB schema directly when in doubt.

## Architecture

### Controllers (`app/Http/Controllers/Admin`)
One controller per resource (Creator, Product, Store, Category, ContentStyle, User) plus workflow-specific controllers: `KirimSampelController` (sample shipment CRUD + import/export + PIC reassignment + chat follow-up), `FollowUpKontenController`, `FollowUpPengirimanController`, `DashboardController`.

Shared behavior lives in traits under `app/Http/Controllers/Concerns`, mixed into controllers rather than a base class:
- `ResolvesListQuery::resolveSort()` — validates `sort`/`dir` query params against a per-controller `SORTABLE_COLUMNS` whitelist before use in `orderBy()`.
- `HandlesBulkDelete::bulkDelete()` — shared bulk-destroy-by-ids logic used by the `*.bulk_destroy` routes.

List/index actions follow a consistent shape: build the Eloquent query with an explicit `select()` + eager-loaded `with()` (only the columns actually used in the view), apply an `applyFilters()`/scope chain, then `search()` scope, `orderBy()`, `paginate(10)->withQueryString()`.

### Models (`app/Models`)
Thin Eloquent models with local query scopes instead of separate query-builder/service classes — e.g. `Creator::scopeSearch`, `scopeFilterDate`; `SendSample::scopeByPic`, `scopeNeedFollowUp`, `scopeFollowUpChat`, `scopeUnfulfilledAgreement`. When adding list filtering, prefer adding a scope on the model over inlining query logic in the controller.

`SendSample` (table `send_sample`) is the central workflow record: links `Creator`, `Store`, `Product`, and two `User` relations (`pic` via `user()`, `approve` via `approver()`). Its scopes encode the business rules for follow-up dashboards (pending shipment, pending content, pending chat) — read these scopes before changing dashboard/follow-up logic, they are the source of truth for "needs follow-up" definitions.

### Import/Export (`app/Imports`, `app/Exports`)
Uses `maatwebsite/excel`. Each importable resource has a matching `*Import` class (e.g. `SendSampleImport`) that resolves human-entered names (toko/kreator/SKU/PIC) to IDs and reports a `skipped` count for rows that didn't match; controllers surface that count in the flash message. `TemplateExport` generates a blank downloadable template from a header array — reuse it for new import templates instead of hand-building a spreadsheet.

### Performance conventions (established the hard way — see git history)
- **Never wrap a datetime/date column in `whereDate()` or similar functions in a `WHERE` clause** — it prevents index usage. Use plain comparisons against `startOfDay()`/`addDay()->startOfDay()` boundaries instead (see `Creator::scopeRecent`, `scopeFilterDate`, `SendSample::scopeFilterDate`).
- Small, slow-changing lookup lists (store dropdown, PIC/user dropdown) are cached via `Cache::remember('dropdown_stores', 86400, ...)` / `dropdown_pics` — follow this pattern for other rarely-changing dropdown sources rather than querying on every request.
- List queries `select()` only the columns the view needs and eager-load relations with column-limited `with(['relation:id,col'])` to avoid over-fetching.
- Creator/product Select2 fields are populated via dedicated AJAX search endpoints (`searchCreators`, `searchProducts`) rather than rendering every row as an `<option>`.

### Views (`resources/views`)
Blade only, organized by feature under `admin/` (`kreator`, `toko`, `product`, `kirim-sampel`, `follow-up-konten`, `follow-up-pengiriman`, `follow-up-chat`, `dashboard`, `users`, `category`, `content-styles`). Shared chrome in `layouts/admin.blade.php` and `partials/admin/`. Dashboard/UI styling follows Material Design 3 tokens.

## Local environment note

MySQL/MariaDB may be reachable from multiple tools in this environment (e.g. an MCP MySQL client vs. the Laravel app's own DB connection) that can point at *different* instances. If DB state observed via a tool doesn't match what the app shows, trust the connection Laravel itself uses (`.env` `DB_*`), not another tool's default.
