# Syntax highlighting is a per-block concern

## Context

ADR-0001 kept the wire blocks-only (no top-level rendering flags) but preserved
`ModalResponse::withoutSyntaxHighlighting()` as a PHP-side **bulk helper**: it
walked the final stack at serialize time and wrote `highlight: false` onto every
`code`/`json` block. It existed for one reason — the single-block sugar
(`ModalResponse::code()`) hands the caller a `ModalResponse`, not the underlying
`CodeBlock`, so there was no handle on which to call the block's own
`->withoutHighlighting()`.

This meant highlighting had two homes (the block's `->withoutHighlighting()` and
the modal's bulk method) and `PayloadBuilder` carried a special-case that reached
into block payloads by type string (`in_array(['code','json'])`).

## Decision

Highlighting lives **only on the blocks that have it** (`code`, `json`):

- The blocks keep `->withoutHighlighting()` (default on) for use inside
  `ModalResponse::stack([...])`.
- The single-block sugar gains a named flag instead of a bulk escape hatch:
  `ModalResponse::code($value, highlight: false)` and
  `ModalResponse::json($value, highlight: false)`, default `true`. Internally the
  flag just conditionally calls the block's `->withoutHighlighting()`.
- `ModalResponse::withoutSyntaxHighlighting()` is **removed**, and
  `PayloadBuilder::build()` drops the `$withoutHighlight` parameter and the
  type-string special-case — it is now a plain `toArray()` map.

This **supersedes the bulk-helper portion of ADR-0001**. The wire stays
blocks-only and the no-top-level-flag decision is unchanged; only the PHP-side
mechanism changes. Removing the public method is a breaking change, taken as part
of the v2 cleanup.

## Considered alternatives

- **Make the sugar return the block (the "first item") so `->withoutHighlighting()`
  works on it directly.** Rejected: a Nova action must return something that
  serializes to the `{ modal: … }` envelope. A bare block does not, so this would
  require making every block `Responsable` (self-wrapping into a single-block
  modal) — and a returned bare block then carries no chrome (`->title()`,
  `->size()`), a real regression on the sugar path. Too much machinery for the
  goal.
- **An abstract chrome class / capability hierarchy on blocks.** Rejected: chrome
  is a modal-level concern; putting `->title()`/`->closeButton()` on blocks is a
  category error (what is `$divider->title()` in a five-block stack?), and
  "a block can be inline and/or base" can't be modelled with PHP single
  inheritance anyway — that is what the `Inlineable`/`Renderable` interfaces are
  for.
- **Keep the bulk helper.** Rejected: two homes for one property and a
  type-aware special-case in `PayloadBuilder`. Per-block control is the one
  obvious mechanism; the named flag covers the single-block convenience.

## Consequences

- To disable highlighting in a multi-block stack, disable it per block:
  `ModalResponse::stack([Block::code('…')->withoutHighlighting()])`. The
  one-shot "disable on every block" convenience is gone by design.
- `PayloadBuilder` no longer knows anything about block types or highlighting.
- Highlighting has a single conceptual home (the `code`/`json` blocks), reached
  either fluently (`->withoutHighlighting()`) or via the sugar's `highlight:`
  flag.
