# Nova Modal Response

A Nova action response that opens a modal. The modal has **chrome** (title, size, close button) and a **body** made of an ordered sequence of typed **blocks**.

## Language

**Modal chrome**:
The frame Nova renders around the modal: header bar (with title), size, footer, close button. Configured via methods on `ModalResponse` (`->title()`, `->size()`, `->closeButton()`, `->withoutSyntaxHighlighting()`).
_Avoid_: header, frame, layout

**Modal title**:
The string in the modal's header bar. Part of modal chrome. Set with `->title()`. Distinct from any `heading` block inside the body.
_Avoid_: name, label

**Stack**:
The ordered sequence of blocks that makes up a modal's body content. Set via `ModalResponse::stack(array|Closure)`.
_Avoid_: body, content list, items

**Block**:
A single, typed unit of modal body content. Blocks render top-to-bottom in the given order. Built-in types: `text`, `heading`, `code`, `json`, `html`, `badge`, `divider`, `list`, `link`, `icon` (plus the authoring-only **view block** and **markdown block**, which both serialize as `html`).
_Avoid_: element, node, section

**View block**:
An authoring-level block that renders a named Blade view server-side and serializes *as an `html` block* — no distinct wire type and no Vue component; the rendered HTML flows through the existing `html` renderer. Built in PHP via `Block::view($view, $data)`, with `ModalResponse::view($view, $data)` sugar to render a view as the whole modal body. Named view files only; a missing view lets Laravel's `View [x] not found` exception bubble.
_Avoid_: blade block, partial, template

**Inline group**:
A block (type `inline`) that lays a set of **atoms** on a single horizontal row, instead of stacking them vertically like the rest of the body. Built in PHP via `Block::inline([...])`. The only block that may contain other blocks.
_Avoid_: row, inline block content, flex container

**Atom**:
A block permitted inside an **inline group**: `text`, `badge`, `icon`, `link`. Marked in PHP by the `Inlineable` interface — a promise about layout only (it may sit on a horizontal row); it does not change the block's serialized output. Blocks that are not atoms (e.g. `heading`, `code`, `divider`) are rejected from an inline group, as is a nested inline group.
_Avoid_: inline element, item, child block

**Markdown block**:
An authoring-time, block-level block built via `Block::markdown($content)`, with `ModalResponse::markdown($content)` sugar to render markdown as the whole modal body. It compiles Markdown to HTML at serialize time (Laravel's `Str::markdown()`, GitHub-flavored) and emits `type: html` on the wire — it has **no wire type and no Vue component of its own**, reusing `HtmlBlock.vue` to keep the single render path (ADR-0001) intact. `$content` is inline markdown by default; chaining `->file()` marks it as a filesystem path instead, which is read and compiled at serialize (a missing/unreadable file then throws). File-based markdown stays available via `ModalResponse::stack([Block::markdown($path)->file()])`. Block-level only (it produces `<h1>`/`<p>`-level HTML), so it is **not** an atom. Same trusted-input model as the `html` block — no sanitization.
_Avoid_: md block, markdown wire type, markdown component

**Heading block**:
A content-level heading inside the modal body. Carries a **visual size** (`small`, `medium`, `large`) — not a semantic HTML level. Distinct from the modal title.
_Avoid_: title, h1/h2, section header

**Variant**:
A semantic colour shared by the blocks that carry one (`badge`, `icon`). Five variants: `default` (neutral grey, the implicit one), `info`, `success`, `warning`, `danger`. Exposed in PHP as zero-arg fluent methods on the block (`->danger()`, …) via the shared `HasVariants` concern; `default` is what you get if no variant method is called.
_Avoid_: badge type, badge color, icon color

**Icon block**:
An inline atom that renders a single Heroicon by `name` via Nova's bundled `Icon` component (from `laravel-nova-ui`, no new dependency), at one fixed size. Coloured by a **variant**. An unknown icon name renders nothing — Nova's `Icon` behaviour; the name set is not validated.
_Avoid_: glyph, image, svg block

**Link block**:
An inline atom that renders an anchor to an `href`. It carries two independent knobs: a **link appearance** (how it looks) and `newTab` (where it opens — `->newTab()` opens a new, secure-by-default tab). A button-appearance link still navigates via its `href`; it does **not** trigger a Nova action.
_Avoid_: button block, action button, CTA

**Link appearance**:
How a link block is rendered: `link` (the implicit one — bold, primary-coloured text) or `button` (button chrome). Cosmetic only; it does not change what the link does. Distinct from **variant**: appearance is shape, not colour.
_Avoid_: link variant, link style, link kind
