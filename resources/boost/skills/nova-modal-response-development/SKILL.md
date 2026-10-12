---
name: nova-modal-response-development
description: Use when a Laravel Nova action should show its result in a modal — code, JSON, text, HTML, Markdown, a Blade view, or a composed stack of blocks (headings, badges, links, collapsibles, tabs). Covers ModalResponse, the Block factory and modal chrome.
---

## Nova Modal Response

`Markwalet\NovaModalResponse\ModalResponse` is a Nova `ActionResponse`. Return it from an action's `handle()` and Nova opens a modal. The modal has **chrome** (title, width, close button) and a **body**: an ordered **stack** of typed **blocks**, built with the `Markwalet\NovaModalResponse\Block` factory.

### When to use

The action's result is something the user should **read or inspect**: generated code, a JSON payload, a report, a status summary, a log.

For other outcomes, return Nova's own responses: `Action::message(...)` for a one-line confirmation, `Action::download(...)` for a file, `Action::redirect(...)` / `Action::visit(...)` for navigation.

### Pick an entry point

One piece of content → a single-block factory:

<code-snippet name="Single-block factories" lang="php">
use Markwalet\NovaModalResponse\ModalResponse;

ModalResponse::text('The export finished successfully.');
ModalResponse::code($generatedScript);                  // highlighted, copy button
ModalResponse::code($log, highlight: false);            // plain text
ModalResponse::json($model->toArray());                 // array in, pretty JSON out
ModalResponse::json($payload, highlight: false);
ModalResponse::html('<strong>Report ready.</strong>');
ModalResponse::markdown("## Done\n\nImported **42** rows.");
ModalResponse::view('reports.summary', ['report' => $report]);
ModalResponse::tabs(['Summary' => [...], 'Log' => [...]]);
</code-snippet>

Several pieces → `ModalResponse::stack([...])`. It takes an array (or a closure returning one) and renders the blocks top to bottom:

<code-snippet name="Stack of blocks" lang="php">
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

public function handle(ActionFields $fields, Collection $models): ModalResponse
{
    return ModalResponse::stack([
        Block::heading('Import finished')->large(),
        Block::inline([
            'Status:',
            Block::badge('3 warnings')->warning()->icon('exclamation-triangle'),
        ]),
        Block::list($warnings),
        Block::divider(),
        Block::collapsible('Raw log', Block::code($log)->withoutHighlighting()),
    ])->title('Import result');
}
</code-snippet>

### Block menu

Every factory is static on `Markwalet\NovaModalResponse\Block`. Fluent options chain on the returned block.

| Factory | Renders | Options |
| --- | --- | --- |
| `text($value)` | One escaped paragraph | — |
| `heading($value)` | Heading inside the body | sizes |
| `list(array $items)` | Bulleted list of escaped strings | `ordered()` |
| `divider()` | Horizontal rule | — |
| `code($value)` | Code with copy button; language auto-detected | `language('sql')`, `withoutHighlighting()` |
| `json(array $value)` | Pretty-printed JSON with copy button | `withoutHighlighting()` |
| `html($value)` | Raw HTML, unsanitized | — |
| `markdown($content)` | GitHub-flavored Markdown compiled to HTML server-side | `file()` — treat `$content` as a file path |
| `view($view, array $data = [])` | Blade view rendered server-side | — |
| `badge($value)` | Coloured label | variants, sizes, `icon($name, trailing: false)` |
| `icon($name)` | One Heroicon by name | variants, sizes |
| `link($label, $href)` | Anchor | `button()`, `newTab()`, sizes, `icon($name, trailing: false)` |
| `inline(array $atoms)` | Atoms on one horizontal row | `spread()`, `center()`, `end()`, `alignment(...)` |
| `collapsible(string $header, $blocks)` | Click-to-expand section, collapsed by default | `expanded(bool\|callable = true)`, `collapsed(bool\|callable = true)` |
| `tabs(array $tabs)` | One visible tab at a time | — (see Tabs) |
| `tab(string $label, array $blocks)` | One tab for `tabs()` | `active(bool\|callable = true)` |

- **Sizes** (heading, badge, icon, link): `small()`, `medium()` (default), `large()`, or `size('large')` / `size(Size::LARGE)`.
- **Variants** (badge, icon): `default()` (default), `info()`, `success()`, `warning()`, `danger()`, or `variant('danger')` / `variant(Variant::DANGER)`.
- Enums live in `Markwalet\NovaModalResponse\Enums` (`Size`, `Variant`, `Alignment`).

### Coercion

- A bare string (or `Stringable`) wherever a block is expected becomes `Block::text(...)`: in `stack()`, `inline()`, `collapsible()`, `tab()` and `tabs()` panels.
- `collapsible()` takes one block or an array of blocks.
- `tabs()` takes `'Label' => [blocks]` pairs and/or `Block::tab(...)` instances; use `Block::tab(...)->active()` to open a tab other than the first.
- Any other value throws `InvalidArgumentException`.

### Atoms vs blocks

Only **atoms** may sit inside `inline([...])`: `text` (and bare strings), `badge`, `icon`, `link`. Every other block — heading, code, list, divider, a nested inline, collapsible, tabs — throws `InvalidArgumentException` there; place those in the stack instead. Atoms also work directly in the stack, each on its own row.

`collapsible()` and `tabs()` are containers: their children can be any blocks, including other collapsibles and tabs.

### Modal chrome

Chain onto any `ModalResponse` (the sugar factories and `stack()` alike):

- `->title(string $title)` — header bar text. A `heading` block is body content, separate from this.
- `->size(string $size)` — modal width, one of Nova's `sm`, `md`, `lg`, `xl`, `2xl` (default) … `7xl`. Unrelated to block sizes.
- `->closeButton(string $label)` — close button label, default `Close`.

### Gotchas

- **Untrusted input:** `html`, `markdown` and `view` output is rendered as HTML without sanitization (Markdown keeps `javascript:` links and most raw HTML). Put user input, request data and AI output in `text`, `list` or `code`, which are escaped — or escape it yourself first.
- **Highlighting** is per block: `highlight: false` on `ModalResponse::code()`/`json()`, `->withoutHighlighting()` on `Block::code()`/`Block::json()`. There is no modal-wide switch.
- **Text is one paragraph:** newlines and extra whitespace collapse. Use `list()` for lines, `code()` for preformatted output, or `markdown()` for formatted prose.
- **JSON takes an array**, not a JSON string; it encodes with `JSON_THROW_ON_ERROR`. To show an existing JSON string, `json_decode($string, true)` it first, or use `code($string)->language('json')`.
- **Links navigate only:** a `button()` link is an anchor styled as a button; it cannot run a Nova action.
- **Icons** are Heroicon names (`check-circle`, `arrow-path`). An unknown name renders nothing, silently.
- **`markdown(...)->file()`** reads the path at serialize time; a missing file throws `InvalidArgumentException`. `view()` with a missing view throws Laravel's `View [x] not found`.
- **Tab labels** render capitalized by CSS; tabs are identified by position, so duplicate labels are allowed.
- **Build through `ModalResponse`.** The payload sent to Vue (`Action::modal('modal-response', [...])`) is internal and changes between majors.

### Reference

Per-block examples: `vendor/markwalet/nova-modal-response/README.md`. Migrating v1 code: `vendor/markwalet/nova-modal-response/UPGRADE.md`.
