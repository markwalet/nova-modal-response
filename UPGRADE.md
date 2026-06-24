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

**No code changes required.** Every v1 method keeps the same signature and the
same observable behavior:

```php
ModalResponse::text('…')->title('…');
ModalResponse::html('…');
ModalResponse::code(file_get_contents(__FILE__))->title('…');
ModalResponse::json(['key' => 'value']);

// chrome setters, all unchanged
->title('…')
->size('7xl')
->closeButton('…')
->withoutSyntaxHighlighting()
```

Under the hood each of these now builds a single-block stack instead of a
flat payload, but you don't have to change anything.

New in v2 — you can now combine content in one modal instead of being limited
to a single shape:

```php
use Markwalet\NovaModalResponse\Blocks\Block;

ModalResponse::stack([
    Block::heading('Import finished')->large(),
    Block::text('Processed 1,204 rows.'),
    Block::badge('3 warnings')->warning(),
    Block::divider(),
    Block::code($logExcerpt)->language('bash'),
])->title('Result');
```

`stack()` also accepts a closure returning the block array. The built-in block
types are `text`, `heading`, `code`, `json`, `html`, `badge`, `divider`, and
`list`.

### `withoutSyntaxHighlighting()`

Same method, same result for single-block use. The only change is on the wire:
v1 emitted a single top-level `highlight: false` flag; v2 writes `highlight:
false` onto every `code` and `json` block in the stack at serialize time. No
action needed unless you read the raw payload (see below).

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

| v1 raw payload key            | v2 replacement                                   |
| ----------------------------- | ------------------------------------------------ |
| `['body' => $text]`           | `ModalResponse::text($text)`                     |
| `['html' => $html]`           | `ModalResponse::html($html)`                     |
| `['code' => $snippet]`        | `ModalResponse::code($snippet)`                  |
| `['code' => json_encode($d)]` | `ModalResponse::json($d)`                        |
| `['highlight' => false]`      | `->withoutSyntaxHighlighting()`                  |
| `['title' => $t]`             | `->title($t)`                                    |
| `['size' => $s]`              | `->size($s)`                                     |
| `['closeButtonText' => $l]`   | `->closeButton($l)`                              |

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
    // …
  ],
}
```

v2 renders the body through a single path: it iterates `data.blocks` and
dispatches each block through an explicit `{ type → component }` map — there is
no `v-if` precedence between content shapes anymore. If you maintain a custom
component, mirror that: iterate the stack and render each block by its `type`.
The bundled `resources/js/components/ModalActionResponse.vue` is the reference
implementation, and each block type has a matching
`resources/js/components/{Type}Block.vue`.

A `console.warn` fires once per modal open if the package detects v1 payload
keys (`body` / `code` / `html` / top-level `highlight`) on the `data` prop.
Migrating to the block stack silences it.
