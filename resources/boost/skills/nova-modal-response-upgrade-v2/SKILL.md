---
name: nova-modal-response-upgrade-v2
description: Upgrade markwalet/nova-modal-response from v1 to v2 in this app. Apply when asked to upgrade, bump, or migrate nova-modal-response (the Nova `ModalResponse` package) to v2 — runs a preflight scan, the composer bump, call-site migration, and verification, then optionally reviews modals for v2 improvements.
---

## Upgrade nova-modal-response to v2

A runbook you execute in the developer's app. It holds the **procedure** only. The **change list** — what broke, what replaces it, what v2 adds — lives in the upgrade guide, the single source of truth:

https://github.com/markwalet/nova-modal-response/blob/main/UPGRADE.md

Fetch it now and keep it open for every step (raw: `https://raw.githubusercontent.com/markwalet/nova-modal-response/main/UPGRADE.md`). After the bump, the same file is also at `vendor/markwalet/nova-modal-response/UPGRADE.md`. Where this runbook and the guide disagree on *what* changed, the guide wins.

Run Phase A in full. Then ask the developer before Phase B.

## Phase A — mechanical upgrade

### A1. Preflight scan (before touching `composer.json`)

PHP, Laravel, and Nova constraints are identical in v1 and v2, so `composer require` succeeds even for an app that v2 will break. The scan is the only safety net. Run every command from the app root (`git grep` searches tracked files only, so `vendor/` and `node_modules/` stay out):

- **S1** — `->withoutSyntaxHighlighting()`: removed in v2, fatal.
  `git grep -n 'withoutSyntaxHighlighting' -- '*.php'`
- **S2** — hand-built payloads (`Action::modal('modal-response', …)`, `withModal('modal-response', …)`): removed in v2, render an empty body.
  `git grep -nE "['\"]modal-response['\"]" -- '*.php'`
- **S3** — code reading the v1 wire format (`body` / `code` / `html` / top-level `highlight`), usually tests asserting on the payload.
  `git grep -lE "ModalResponse|modal-response" -- '*.php' '*.js' '*.ts' '*.vue'`, then read each listed file for reads of those keys off the modal payload (e.g. `->jsonSerialize()['modal']->payload['code']`, `modal.payload.body`).
- **S4** — a custom Vue override of the `modal-response` component.
  `git grep -nE "['\"]modal-response['\"]" -- '*.js' '*.ts' '*.vue' ':!public/*' ':!*dist/*'` (the excludes skip compiled bundles; the source file is what counts)

Report every hit to the developer, grouped by S1–S4, with file and line. Done when all four scans have run and their hits are listed.

**S4 hit = HARD STOP.** The developer owns that component and its build. Report the file(s), point them to the guide's "If you overrode the `modal-response` Vue component" section, and end the run here — no migration, no bump, no edits to their JS. Resume from A2 only once the developer confirms the override is migrated or removed.

### A2. Migrate on v1

Migrate S1 and S2 while the app is still on v1. This skill ships only in the latest 1.x release, so the app is already on ≥ v1.1.3, where `highlight:` exists. Migrating first keeps the app green at every step and makes the bump itself a no-op code-wise.

Rewrite every S1 and S2 hit using the guide's replacements:

- S1: move the flag onto the factory — `ModalResponse::code($x)->withoutSyntaxHighlighting()` becomes `ModalResponse::code($x, highlight: false)` (same for `json()`). On a `text()`/`html()` response the call did nothing; delete it.
- S2: rebuild the payload with the `ModalResponse` API, following the guide's v1-key → v2 table.

S3 waits for A5: those readers expect whatever shape the installed version emits, so they change together with the bump.

Keep each migrated modal's rendered output equivalent to v1 — improvements belong to Phase B.

Done when S1 and S2 return zero hits.

### A3. Verify on v1

Run the app's test suite and static analysis, if it has them (check `composer.json` scripts). Done when both are green — fix any failure from A2 before moving on, so a later failure can only come from the bump.

### A4. Bump

```bash
composer require markwalet/nova-modal-response:^2.0
php artisan boost:update
```

`boost:update` replaces the installed v1 `nova-modal-response-development` skill with the v2 one. v2 does not ship this upgrade skill, so Boost deletes it from the skills directory as stale — expected; carry on from the copy already in your context.

Done when `composer show markwalet/nova-modal-response` reports a `2.x` version and the installed `nova-modal-response-development/SKILL.md` in the agent skills directory (e.g. `.claude/skills/`) is identical to `vendor/markwalet/nova-modal-response/resources/boost/skills/nova-modal-response-development/SKILL.md`. If `boost:update` installs no nova-modal-response skill, check that `markwalet/nova-modal-response` is listed under `packages` in `boost.json` and tell the developer if it is missing.

### A5. Migrate wire-format readers

Rewrite every S3 hit to read the v2 `blocks` array described in the guide. Done when every S3 file reads only the v2 shape.

### A6. Verify on v2

Two halves, reported separately.

**Automated (you run these):**

- `composer show markwalet/nova-modal-response` → `2.x`.
- S1 and S2 re-run → zero hits.
- The app's test suite and static analysis, if it has them (check `composer.json` scripts) → green.

**Human (you cannot do these):** you have no browser, so the static checks cannot prove a modal renders. Give the developer a checklist of every Nova action that returns a modal touched in A2 (action class + the resource it is registered on), and ask them to trigger each once and confirm:

- the modal body renders as before;
- the browser console shows no `[nova-modal-response] Legacy payload keys detected` warning.

Phase A is complete when the automated half is green and the checklist is handed over.

## Ask the developer

> Phase A is done: the app runs on v2 with a 1:1 migration. Stop here, or should I also review your modals for v2 improvements (Phase B)?

Stop unless they opt in.

## Phase B — v2 improvements (opt-in)

v2 replaces the single-shape modal body with a **stack** of typed blocks. Derive the available blocks and their API from two sources, read now:

- `vendor/markwalet/nova-modal-response/resources/boost/skills/nova-modal-response-development/SKILL.md`
- the guide's "New in v2" material

Use only blocks and methods those sources document.

1. **Inventory.** List every `ModalResponse::` call site: `git grep -n 'ModalResponse::' -- '*.php'`. Done when each site is listed with what it currently shows.
2. **Propose.** For each site where the stack makes the modal clearer, write a before/after: the current code, the proposed code, and one line on what the reader gains. Typical shapes: a raw JSON dump becomes a heading, a row of status/ID badges, and the JSON; a request/response pair becomes tabs; a long secondary payload goes into a collapsible. A site that already reads well gets "no change".
3. **Confirm.** Present all proposals and ask which to apply. Apply exactly the confirmed sites; every other modal stays as Phase A left it.
4. **Verify.** Re-run the automated half of A6, and add every changed modal to the developer's browser checklist.
