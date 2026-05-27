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
A single, typed unit of modal body content. Blocks render top-to-bottom in the given order. Built-in types: `text`, `heading`, `code`, `json`, `html`, `badge`, `divider`, `list`, `link`.
_Avoid_: element, node, section

**Heading block**:
A content-level heading inside the modal body. Carries a **visual size** (`small`, `medium`, `large`) — not a semantic HTML level. Distinct from the modal title.
_Avoid_: title, h1/h2, section header

**Badge variant**:
A semantic colour for a `badge` block. Five variants: `default` (neutral grey, the implicit one), `info`, `success`, `warning`, `danger`. Exposed in PHP as zero-arg fluent methods on the block (`->danger()`, …); `default` is what you get if no variant method is called.
_Avoid_: badge type, badge color

**Link block**:
An inline atom that renders an anchor to an `href`. It carries two independent knobs: a **link appearance** (how it looks) and `newTab` (where it opens — `->newTab()` opens a new, secure-by-default tab). A button-appearance link still navigates via its `href`; it does **not** trigger a Nova action.
_Avoid_: button block, action button, CTA

**Link appearance**:
How a link block is rendered: `link` (the implicit one — bold, primary-coloured text) or `button` (button chrome). Cosmetic only; it does not change what the link does. Distinct from **badge variant**: appearance is shape, not colour.
_Avoid_: link variant, link style, link kind
