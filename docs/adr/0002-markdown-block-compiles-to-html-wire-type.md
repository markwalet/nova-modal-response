# Markdown block compiles to the existing `html` wire type

## Context

ADR-0001 established a uniform render pipeline: each block type maps one-to-one
to a Vue component through an explicit `{ type → component }` map, and adding a
block is "a new PHP factory + a new Vue component + an entry in the component
map". The codebase reinforces this with a `{Type}Block.php ↔ {Type}Block.vue ↔
type` naming convention.

A Markdown block breaks the symmetry of that convention. Markdown is a *source
format*, not a *render target*: GitHub-flavored Markdown compiles to ordinary
block-level HTML (`<h1>`, `<p>`, `<ul>`, tables, …). There are two ways to ship
it:

1. Introduce a `markdown` wire type with its own `MarkdownBlock.vue` that renders
   Markdown client-side (a new JS dependency, e.g. a browser Markdown parser).
2. Compile to HTML in PHP at serialize time and emit `type: html` — no new wire
   type, no Vue component, no JS dependency.

## Decision

`MarkdownBlock` compiles its content with Laravel's `Str::markdown()`
(bundled `league/commonmark`, GitHub-flavored, safe-by-default config) at
`toArray()` time and emits `{ type: 'html', value: '<compiled html>' }`. It has
**no wire type and no Vue component of its own** — it reuses `HtmlBlock.vue`.

This is a deliberate, hard-to-reverse break of the
`{Type}Block ↔ {Type}Block.vue ↔ type` convention: `MarkdownBlock.php` exists
with no matching `MarkdownBlock.vue` and no `markdown` entry in the component
map. The rationale is recorded here so a future reader does not "fix" the
apparent inconsistency by adding a client-side renderer.

## Considered alternatives

- **A distinct `markdown` wire type with client-side rendering.** Rejected: it
  adds a second render path for content that is already HTML, pulls in a JS
  Markdown dependency, and would mean the wire carries un-compiled Markdown
  (raw source) to the browser — a larger, less-safe payload than the compiled,
  escaped HTML. It buys nothing over compiling in PHP.
- **Two separate sources (`content` and `file` constructor params).** Rejected
  in favour of a single `$content` source whose interpretation is switched by a
  fluent `->file()` marker (`Block::markdown($content)` inline by default,
  `Block::markdown($path)->file()` to read a file). Avoids the both/neither
  ambiguity entirely instead of guarding it; an unreadable path under `->file()`
  still throws `InvalidArgumentException` at serialize.

## Consequences

- The block is authoring-time only: `Block::markdown()` is the entire public
  surface. There is no `markdown` type on the wire, so nothing downstream
  (Vue, the component map, the wire-format docs) gains a branch.
- A dev who needs raw, un-escaped HTML inside a document drops an `html` block
  alongside — same trusted-input model, no new sanitizer.
- Converter options/extensions are intentionally out of scope; they can be added
  later as additive fluent methods without touching the wire format.
