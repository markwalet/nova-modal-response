# Upgrade Guide

- [Upgrading To 2.0 From 1.x](#upgrading-to-20-from-1x)

## High Impact Changes

- [Updating Dependencies](#updating-dependencies)

## Medium Impact Changes

- [`withoutSyntaxHighlighting()` Removed](#withoutsyntaxhighlighting-removed)
- [Payload Assertions In Tests](#payload-assertions-in-tests)

## Low Impact Changes

- [Hand-Built `modal-response` Payloads](#hand-built-modal-response-payloads)
- [Custom Vue Component](#custom-vue-component)

## Upgrading To 2.0 From 1.x

#### Estimated Upgrade Time: 5 Minutes

> [!NOTE]
> We attempt to document every possible breaking change. Since some of these breaking changes are in obscure parts of the package, only a portion of them may actually affect your application. If you only build modals through the `ModalResponse` PHP API, updating the dependency is usually all it takes.

> [!TIP]
> Using [Laravel Boost](https://github.com/laravel/boost)? Update to the latest 1.x release first: it ships an upgrade skill. Ask your coding agent to upgrade nova-modal-response to v2 and it scans your app, migrates what this guide describes, and verifies the result.

v2 reshapes the modal body into an ordered stack of typed blocks. The PHP API you already use is preserved, but the payload sent to the browser changed. For what's new, see the [README](README.md#blocks).

### Updating Dependencies

**Likelihood Of Impact: High**

Update the package constraint in your application's `composer.json`:

```bash
composer require markwalet/nova-modal-response:^2.0
```

The platform requirements are unchanged: PHP `^8.2`, `laravel/framework ^12.0|^13.0` and `laravel/nova ^5.0`.

If you use Laravel Boost, run `php artisan boost:update` afterwards so your agents get the v2 version of the package's development skill.

The compiled frontend assets ship with the package. You only need to rebuild anything if you maintain your own build of this package's frontend.

### Syntax Highlighting

<a name="withoutsyntaxhighlighting-removed"></a>
#### `withoutSyntaxHighlighting()` Removed

**Likelihood Of Impact: Medium**

The `->withoutSyntaxHighlighting()` method was deprecated in v1.1.3 and has been removed. Syntax highlighting is now set per `code` or `json` block.

For the single-block factories, pass `highlight: false` instead. This argument is available since v1.1.3, so you can migrate before updating:

```php
// v1
ModalResponse::code($snippet)->withoutSyntaxHighlighting();
ModalResponse::json($data)->withoutSyntaxHighlighting();

// v2
ModalResponse::code($snippet, highlight: false);
ModalResponse::json($data, highlight: false);
```

Inside a stack, call `withoutHighlighting()` on each block that should render as plain text:

```php
ModalResponse::stack([
    Block::code($snippet)->withoutHighlighting(),
    Block::json($data)->withoutHighlighting(),
]);
```

### Testing

<a name="payload-assertions-in-tests"></a>
#### Payload Assertions In Tests

**Likelihood Of Impact: Medium**

The modal payload moved from flat keys to a `blocks` array. Tests that read `payload['code']`, `payload['body']` or `payload['html']` need updating. Find the block by its `type` rather than by index, so the test keeps passing when blocks are added around it:

```php
// v1
$data = json_decode($response['modal']->payload['code'], true);

// v2
$blocks = $response['modal']->payload['blocks'];
$this->assertIsArray($blocks);

$block = collect($blocks)->firstWhere('type', 'json');
$data = json_decode($block['value'], true);
```

The `assertIsArray()` call narrows the payload's type, so this snippet passes PHPStan up to level 8 as written.

This is what the single-block factories emit:

```php
ModalResponse::json(['ok' => true])->title('Result');
// ['title' => 'Result', 'blocks' => [
//     ['type' => 'json', 'value' => "{\n    \"ok\": true\n}", 'highlight' => true],
// ]]

ModalResponse::code('echo 1;', highlight: false);
// ['blocks' => [['type' => 'code', 'value' => 'echo 1;', 'highlight' => false]]]

ModalResponse::text('Done');     // ['blocks' => [['type' => 'text', 'value' => 'Done']]]
ModalResponse::html('<b>x</b>'); // ['blocks' => [['type' => 'html', 'value' => '<b>x</b>']]]
```

- `json` blocks carry the pretty-printed JSON string in `value`, as v1's `code` key did.
- `code` blocks only include `language` when you set one with `->language()`.
- `markdown` and `view` blocks arrive as `type: html`, with the rendered HTML in `value`.
- The `title`, `size` and `closeButtonText` keys are only present when set.

### Payload Format

<a name="hand-built-modal-response-payloads"></a>
#### Hand-Built `modal-response` Payloads

**Likelihood Of Impact: Low**

v1 documented building the payload directly with `Action::modal('modal-response', [...])`. This pattern no longer works:

```php
// v1: no longer works
return Action::modal('modal-response', [
    'title' => 'Result',
    'body' => 'Done!',
]);
```

The `body`, `code` and `html` keys and the top-level `highlight` flag no longer exist; the body is now a `blocks` array. There is no compatibility layer: a v1 payload renders an empty modal body and logs a console warning (see [Custom Vue Component](#custom-vue-component)).

Use the `ModalResponse` PHP API instead, which builds the correct payload for you:

| v1 payload key                | v2 replacement                                    |
| ----------------------------- | ------------------------------------------------- |
| `['body' => $text]`           | `ModalResponse::text($text)`                      |
| `['html' => $html]`           | `ModalResponse::html($html)`                      |
| `['code' => $snippet]`        | `ModalResponse::code($snippet)`                   |
| `['code' => json_encode($d)]` | `ModalResponse::json($d)`                         |
| `['highlight' => false]`      | `ModalResponse::code($snippet, highlight: false)` |
| `['title' => $t]`             | `->title($t)`                                     |
| `['size' => $s]`              | `->size($s)`                                      |
| `['closeButtonText' => $l]`   | `->closeButton($l)`                               |

For anything more than a single piece of content, use `ModalResponse::stack([...])` with `Block` instances.

### Frontend

<a name="custom-vue-component"></a>
#### Custom Vue Component

**Likelihood Of Impact: Low**

If you override the `modal-response` Vue component, its `data` prop changed shape. v1 exposed `data.body`, `data.code`, `data.html` and `data.highlight`; v2 exposes a single `data.blocks` array:

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

This is a partial list. The full set of types is the map in `resources/js/components/blockComponents.js`, and each block's exact keys are in its `toArray()` in `src/Blocks/`.

v2 renders the body through a single path: it iterates `data.blocks` and renders each block through an explicit `{ type → component }` map, with no `v-if` precedence between content shapes. If you maintain a custom component, do the same. The bundled `resources/js/components/ModalActionResponse.vue` is the reference implementation, and each wire `type` has a matching `resources/js/components/{Type}Block.vue`. The `markdown` and `view` blocks have no wire type of their own: they are rendered to HTML on the server and arrive as `html` blocks.

A `console.warn` fires once per modal open when the component receives v1 payload keys (`body`, `code`, `html` or a top-level `highlight`). Migrating to the block stack silences it.
