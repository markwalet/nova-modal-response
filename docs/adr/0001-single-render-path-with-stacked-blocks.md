# Single render path with stacked content blocks

## Context

v1 exposed four mutually-exclusive content factories on `ModalResponse` (`text`, `html`, `code`, `json`) and the Vue component (`ModalActionResponse.vue`) rendered one shape via `v-if` branching, with hard precedence (`code` silently shadowed `html`/`body`). Callers who wanted to combine content — e.g. an explanatory paragraph above a JSON dump — had to drop into raw `html()` and lose syntax highlighting on the way.

The README publicly documented the underlying `Action::modal('modal-response', ['body' => ...])` payload shape, so any rework of the wire format is a breaking change.

## Decision

The modal body is an ordered **stack** of typed **blocks** at every layer:

- **PHP model**: `ModalResponse::stack(array|Closure $blocks)` is the primary entry point. The legacy `text/code/html/json` factories survive as one-line sugar that each build a single-block stack.
- **Wire format**: `{ title, size, closeButtonText, blocks: [{ type, value, … }, …] }`. No top-level rendering flags; everything that affects how a block renders lives on that block.
- **Vue render**: `ModalActionResponse.vue` iterates `data.blocks` and dispatches each through an explicit `{ type → component }` map. Exactly one render path. No legacy precedence branching.

Ship as **v2.0** with a hard wire-format break — no Vue-side compatibility shim for the legacy `{ body, html, code }` payload.

## Considered alternatives

- **Vue-side shim for the legacy payload shape.** Rejected: it permanently doubles the number of render paths Vue has to support and undermines the entire point of the rework — a single, simple render code path.
- **Top-level `highlight` flag on the wire with a Vue-side cascade to blocks.** Rejected: it would mean Vue has two sources of truth (modal-level default + per-block override) for the same property. Instead, `ModalResponse::withoutSyntaxHighlighting()` survives as a PHP-side bulk helper that walks the final stack at serialize time and writes `highlight: false` to every code/json block. Wire stays blocks-only.
- **Semantic `<h1>`–`<h6>` for the heading block.** Rejected: the modal title is already the implicit document heading, and Nova's typography doesn't map cleanly onto h-levels inside a modal body. Heading blocks instead carry a **visual size** (`small`/`medium`/`large`).

## Consequences

- Direct `Action::modal('modal-response', ['body' => ...])` callers break on upgrade. Documented in the v2.0 changelog and the rewritten README.
- The PHP-side `ModalResponse::text/code/html/json` convenience methods keep working unchanged on the public surface; only their emitted wire format changes.
- Adding new block types in future minor releases is additive and non-breaking: a new PHP factory + a new Vue component + an entry in the component map.
