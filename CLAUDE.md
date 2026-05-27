# CLAUDE.md

## Agent skills

### Issue tracker

Issues live in GitHub Issues for `markwalet/nova-modal-response`, accessed via the `gh` CLI. See `docs/agents/issue-tracker.md`.

### Triage labels

Canonical label vocabulary (`needs-triage`, `needs-info`, `ready-for-agent`, `ready-for-human`, `wontfix`). See `docs/agents/triage-labels.md`.

### Domain docs

Single-context layout: one `CONTEXT.md` and `docs/adr/` at the repo root. See `docs/agents/domain.md`.

### Assets

Don't compile or commit `dist/` in PRs — edit only `resources/`, but still run QA & tests. Assets are built once per release via `composer release-build`. See `docs/agents/assets.md`.
