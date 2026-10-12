# Upgrading to v2

v2 reshapes the modal body into an ordered **stack** of typed **blocks**. The
PHP API you already use is preserved, but the payload sent to the browser (the
**wire format**) changed, and one published shortcut is removed.

Read the section that matches how you use the package. Most callers only need
the first one.

## Requirements

Unchanged from v1: PHP `^8.2`, `laravel/framework ^12.0|^13.0`,
`laravel/nova ^5.0`. Upgrading is a version bump:

```bash
composer require markwalet/nova-modal-response:^2.0
```

If you publish or override this package's frontend assets, rebuild them after
upgrading (the compiled `dist/` ships with the package, so this only applies if
you maintain your own build).

## If you only use the `ModalResponse` PHP API

**Almost no code changes required.** Every v1 method except
`->withoutSyntaxHighlighting()` keeps the same signature and the same observable
behavior:

```php
ModalResponse::text('…')->title('…');
ModalResponse::html('…');
ModalResponse::code(file_get_contents(__FILE__))->title('…');
ModalResponse::json(['key' => 'value']);

// chrome setters, all unchanged
->title('…')
->size('7xl')
->closeButton('…')
```

Under the hood each of these now builds a single-block stack instead of a
flat payload, but you don't have to change anything.

New in v2 — you can now combine content in one modal instead of being limited
to a single shape:

```php
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

ModalResponse::stack([
    Block::heading('Import finished')->large(),
    Block::text('Processed 1,204 rows.'),
    Block::badge('3 warnings')->warning(),
    Block::divider(),
    Block::code($logExcerpt)->language('bash'),
])->title('Result');
```

`stack()` also accepts a closure returning the block array, and bare strings
in the array become text blocks. See the [README](README.md#blocks) for every
available block.

### `withoutSyntaxHighlighting()` is removed

`->withoutSyntaxHighlighting()` was deprecated in v1.1.3 and is removed in v2. Syntax highlighting is now set per `code`/`json` block.

For the single-block factories, pass `highlight: false` (available since
v1.1.3):

```php
// v1
ModalResponse::code($snippet)->withoutSyntaxHighlighting();
ModalResponse::json($data)->withoutSyntaxHighlighting();

// v2
ModalResponse::code($snippet, highlight: false);
ModalResponse::json($data, highlight: false);
```

Inside a stack, call `withoutHighlighting()` on each block that should render
as plain text:

```php
ModalResponse::stack([
    Block::code($snippet)->withoutHighlighting(),
    Block::json($data)->withoutHighlighting(),
]);
```

## If your tests assert on the payload

The modal payload moved from flat keys to a `blocks` array, so assertions that
read `payload['code']`, `payload['body']` or `payload['html']` need updating.
Find the block by its `type` rather than by index, so the test keeps passing
when blocks are added around it:

```php
$payload = $response['modal']->payload;

// v1
$data = json_decode($payload['code'], true);

// v2
$block = collect($payload['blocks'])->firstWhere('type', 'json');
$data = json_decode($block['value'], true);
```

What the single-block factories emit:

```php
ModalResponse::json(['ok' => true])->title('Result');
// ['title' => 'Result', 'blocks' => [
//     ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => true],
// ]]

ModalResponse::code('echo 1;', highlight: false);
// ['blocks' => [['type' => 'code', 'value' => 'echo 1;', 'highlight' => false]]]

ModalResponse::text('Done');   // ['blocks' => [['type' => 'text', 'value' => 'Done']]]
ModalResponse::html('<b>x</b>'); // ['blocks' => [['type' => 'html', 'value' => '<b>x</b>']]]
```

- `json` blocks carry the pretty-printed JSON string in `value`, as v1's
  `code` key did.
- `code` blocks only include `language` when you set one with `->language()`.
- `markdown` and `view` blocks arrive as `type: html` with the rendered HTML in
  `value`.
- Chrome keys (`title`, `size`, `closeButtonText`) are only present when set.

## If you hand-built the payload with `Action::modal('modal-response', …)`

**This pattern is removed in v2.** v1 documented building the payload directly:

```php
// v1 — no longer works
return Action::modal('modal-response', [
    'title' => 'Result',
    'body'  => 'Done!',
]);
```

The keys `body`, `code`, `html`, and the top-level `highlight` flag no longer
exist on the wire — the body is now a `blocks` array. There is no
compatibility shim; a legacy payload renders an empty modal body (and logs a
console warning — see the next section).

Move to the `ModalResponse` PHP API, which produces the correct v2 payload for
you:

| v1 raw payload key            | v2 replacement                                    |
| ----------------------------- | ------------------------------------------------- |
| `['body' => $text]`           | `ModalResponse::text($text)`                      |
| `['html' => $html]`           | `ModalResponse::html($html)`                      |
| `['code' => $snippet]`        | `ModalResponse::code($snippet)`                   |
| `['code' => json_encode($d)]` | `ModalResponse::json($d)`                         |
| `['highlight' => false]`      | `ModalResponse::code($snippet, highlight: false)` |
| `['title' => $t]`             | `->title($t)`                                     |
| `['size' => $s]`              | `->size($s)`                                      |
| `['closeButtonText' => $l]`   | `->closeButton($l)`                               |

For anything more than a single shape, reach for `ModalResponse::stack([...])`
with explicit `Block` instances.

## If you overrode the `modal-response` Vue component

The component's `data` prop changed shape. v1 exposed `data.body`, `data.code`,
`data.html`, and `data.highlight`; v2 exposes a single `data.blocks` array:

```js
{
  title: '…',
  size: '2xl',
  closeButtonText: 'Close',
  blocks: [
    { type: 'text', value: '…' },
    { type: 'code', value: '…', highlight: true, language: 'bash' },
    { type: 'json', value: '{\n    "ok": true\n}', highlight: true },
    { type: 'badge', value: 'Live', variant: 'success', size: 'medium', icon: null, iconPosition: 'leading' },
    { type: 'inline', alignment: 'spread', value: [ /* atom blocks */ ] },
    { type: 'tabs', value: [{ label: 'Log', active: false, value: [ /* blocks */ ] }] },
    // …
  ],
}
```

This is a partial list. The full set of types is the map in
`resources/js/components/blockComponents.js`, and each block's exact keys are
in its `toArray()` in `src/Blocks/`.

v2 renders the body through a single path: it iterates `data.blocks` and
dispatches each block through an explicit `{ type → component }` map — there is
no `v-if` precedence between content shapes anymore. If you maintain a custom
component, mirror that: iterate the stack and render each block by its `type`.
The bundled `resources/js/components/ModalActionResponse.vue` is the reference
implementation, the map lives in `resources/js/components/blockComponents.js`,
and each wire `type` has a matching `resources/js/components/{Type}Block.vue`.
The `markdown` and `view` blocks have no wire type of their own: they are
rendered to HTML on the server and arrive as `html` blocks.

A `console.warn` fires once per modal open if the package detects v1 payload
keys (`body` / `code` / `html` / top-level `highlight`) on the `data` prop.
Migrating to the block stack silences it.
