# Nova Modal Response

[![Build Status](https://github.com/markwalet/nova-modal-response/workflows/tests/badge.svg)](https://github.com/markwalet/nova-modal-response/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/markwalet/nova-modal-response)](https://packagist.org/packages/markwalet/nova-modal-response)
[![Latest Stable Version](https://img.shields.io/packagist/v/markwalet/nova-modal-response)](https://packagist.org/packages/markwalet/nova-modal-response)
[![License](https://img.shields.io/packagist/l/markwalet/nova-modal-response)](https://packagist.org/packages/markwalet/nova-modal-response)

Enabling custom modals for action responses in [Laravel Nova](https://nova.laravel.com).

![A Nova page with a modal showing a heading, status badges and the highlighted PHP that builds it, split between light and dark mode](art/hero.png)

More info for this specific feature can be found in the [Nova Documentation](https://nova.laravel.com/docs/v5/actions/defining-actions#custom-modal-responses).

## Installation

```shell
composer require markwalet/nova-modal-response
```

This package requires PHP 8.2+, Laravel 12+ and Nova 5.

Upgrading from v1? See [UPGRADE.md](UPGRADE.md).

## Usage

Return a `ModalResponse` from your action's `handle()` method:

```php
use Markwalet\NovaModalResponse\ModalResponse;

public function handle(ActionFields $fields, Collection $models)
{
    return ModalResponse::text('This is way better than that small notification in the bottom right!')
        ->title('Result in a modal');
}
```

### Quick start

For a modal with a single piece of content, use one of the one-line factories:

```php
ModalResponse::text('Export finished.');
ModalResponse::html('<strong>Report ready.</strong> Check your inbox.');
ModalResponse::code(file_get_contents(__FILE__));
ModalResponse::json(['lorem' => 'ipsum', 'dolor' => ['sit', 'amet']]);
ModalResponse::markdown("## Done\n\nImported **42** rows.");
ModalResponse::view('reports.summary', ['report' => $report]);
```

<p align="center"><img src="art/one-liner.png" alt="ModalResponse::json() rendering a highlighted JSON payload" width="600"></p>

`code()` and `json()` are syntax highlighted with [highlight.js](https://highlightjs.org/). Pass `highlight: false` to turn that off:

```php
ModalResponse::code($log, highlight: false);
```

### Modal chrome

Chain these onto any `ModalResponse`:

| Method                  | Default | Description                                      |
| ----------------------- | ------- | ------------------------------------------------ |
| `->title(string)`       | none    | Text in the modal's header bar.                  |
| `->size(string)`        | `2xl`   | Modal width: one of Nova's sizes, `sm` to `7xl`. |
| `->closeButton(string)` | `Close` | Label of the close button.                       |

```php
ModalResponse::text('Hello world')
    ->title('Greeting')
    ->size('7xl')
    ->closeButton('Got it');
```

### Stacking blocks

To combine content, pass an ordered list of blocks to `ModalResponse::stack()`. Blocks render top to bottom:

```php
use Markwalet\NovaModalResponse\Block;
use Markwalet\NovaModalResponse\ModalResponse;

return ModalResponse::stack([
    Block::heading('Import finished')->large(),
    Block::text('Processed 1,204 rows.'),
    Block::badge('3 warnings')->warning(),
    Block::divider(),
    Block::code($logExcerpt)->language('bash'),
])->title('Result');
```

- `stack()` also accepts a closure that returns the array.
- A bare string anywhere a block is expected becomes a `text` block, so `'Processed 1,204 rows.'` works as well. Anything else that isn't a block throws an `InvalidArgumentException`.

### Blocks

All blocks are built through the `Markwalet\NovaModalResponse\Block` factory.

| Category                                      | Blocks                                                                    |
| --------------------------------------------- | ------------------------------------------------------------------------- |
| [Text content](#text-content)                 | [text](#text) · [heading](#heading) · [list](#list) · [divider](#divider) |
| [Code and data](#code-and-data)               | [code](#code) · [json](#json)                                             |
| [Inline elements](#inline-elements)           | [badge](#badge) · [icon](#icon) · [link](#link) · [inline](#inline)       |
| [Collapsible and tabs](#collapsible-and-tabs) | [collapsible](#collapsible) · [tabs](#tabs)                               |
| [Raw HTML](#raw-html)                         | [html](#html) · [markdown](#markdown) · [view](#view)                     |

#### Text content

```php
ModalResponse::stack([
    Block::heading('Import finished')->large(),
    Block::text('Processed 1,204 rows from customers.csv. Three rows were skipped and need a manual fix.'),
    Block::divider(),
    Block::heading('Skipped rows')->small(),
    Block::list([
        'Row 18: missing email address',
        'Row 211: duplicate customer number',
        'Row 940: unknown country code "UK"',
    ]),
    Block::heading('Next steps')->small(),
    Block::list([
        'Correct the rows in the source file',
        'Re-run the import with "Only failed rows"',
    ])->ordered(),
]);
```

<p align="center"><img src="art/blocks-text.png" alt="A large heading, a paragraph, a divider, small headings, a bulleted list and a numbered list" width="600"></p>

##### Text

A single paragraph of escaped text. Whitespace and newlines are not preserved; use `code` or `list` for multi-line output.

```php
Block::text('The export finished successfully.');
```

##### Heading

A heading inside the body (separate from the modal title). Sizes: `small()`, `medium()` (default), `large()`.

```php
Block::heading('Summary')->large();
```

##### List

A list of escaped strings. Unordered by default; call `ordered()` for a numbered list.

```php
Block::list(['apples', 'oranges', 'pears']);
Block::list(['Back up', 'Migrate', 'Verify'])->ordered();
```

##### Divider

A horizontal rule between blocks.

```php
Block::divider();
```

#### Code and data

```php
ModalResponse::stack([
    Block::heading('Query')->small(),
    Block::code($sql)->language('sql'),
    Block::heading('Result')->small(),
    Block::json($customer),
]);
```

<p align="center"><img src="art/blocks-code.png" alt="A highlighted SQL query and its JSON result, each with a copy button" width="600"></p>

##### Code

A code snippet with a copy button. highlight.js auto-detects the language unless you set one with `language()`. Call `withoutHighlighting()` to render plain text.

```php
Block::code('SELECT * FROM users;')->language('sql');
Block::code($log)->withoutHighlighting();
```

##### JSON

Takes an array, pretty-prints it as JSON and highlights it. Has a copy button. Supports `withoutHighlighting()`.

```php
Block::json($user->toArray());
```

#### Inline elements

```php
ModalResponse::stack([
    Block::inline([Block::icon('check-circle')->success()->large(), 'Paid with Visa', Block::badge('•••• 4242')]),
    Block::inline(['Fulfilment', Block::badge('Shipped')->success()->icon('truck')])->spread(),
    Block::inline(['Priority', Block::badge('High')->warning()])->spread(),
    Block::inline(['Fraud check', Block::badge('Review')->danger()->icon('exclamation-triangle')])->spread(),
    Block::divider(),
    Block::inline([
        Block::badge('B2B'),
        Block::badge('Returning')->info(),
        Block::badge('Verified')->success(),
        Block::badge('Backorder')->warning(),
        Block::badge('Chargeback risk')->danger(),
    ]),
    Block::divider(),
    Block::inline([
        Block::link('Open in Stripe', $stripeUrl)->button()->newTab()->icon('arrow-top-right-on-square', trailing: true),
        Block::link('Download invoice', $invoiceUrl)->icon('arrow-down-tray'),
        Block::link('Customer profile', $profileUrl),
    ]),
]);
```

<p align="center"><img src="art/blocks-inline.png" alt="Inline rows with an icon, key/value rows with badges, badges in every variant, and links" width="600"></p>

##### Badge

A small coloured label. Variants: `default()` (default), `info()`, `success()`, `warning()`, `danger()`. Sizes: `small()`, `medium()` (default), `large()`. Add a [Heroicon](https://heroicons.com) inside the badge with `icon($name)`; pass `trailing: true` to put it after the label.

```php
Block::badge('Published')->success();
Block::badge('Deploying')->info()->icon('arrow-path', trailing: true);
```

##### Icon

A single [Heroicon](https://heroicons.com) by name. Supports the same variants and sizes as the badge. Unknown names render nothing.

```php
Block::icon('exclamation-triangle')->warning()->large();
```

##### Link

A link to a URL. Call `button()` to render it as a button, `newTab()` to open it in a new tab, and `icon($name, trailing: false)` to add an icon. Sizes: `small()`, `medium()` (default), `large()`. A button-style link still just navigates to its URL; it does not run a Nova action.

```php
Block::link('Open invoice', route('invoices.show', $invoice));
Block::link('Download', $url)->button()->newTab()->icon('arrow-down-tray');
```

##### Inline

Puts a few small blocks on one horizontal row. Only `text`, `badge`, `icon` and `link` blocks (and bare strings) are allowed inside; anything else throws an `InvalidArgumentException`. Alignment: start (default), `spread()`, `center()` or `end()`.

```php
Block::inline([
    'Deployment',
    Block::badge('Live')->success(),
])->spread();
```

#### Collapsible and tabs

```php
ModalResponse::tabs([
    'Overview' => [
        Block::inline(['Customer', 'Ada Lovelace'])->spread(),
        Block::inline(['Lifetime value', Block::badge('€ 4,210')->success()])->spread(),
        Block::inline(['Open tickets', Block::badge('2')->warning()])->spread(),
        Block::collapsible('Billing address', Block::json($address))->expanded(),
        Block::collapsible('Internal notes', 'Prefers invoices by post.'),
    ],
    'Orders' => [Block::list($orders)],
    'Activity' => ['Last login 3 days ago.'],
]);
```

<p align="center"><img src="art/blocks-containers.png" alt="Tabs with key/value rows, an expanded collapsible with JSON and a collapsed one" width="600"></p>

##### Collapsible

A header the user clicks to show or hide its content. Takes a single block or an array of blocks, which can be any blocks. Starts collapsed; call `expanded()` to start open. Both `expanded()` and `collapsed()` accept a boolean or a closure.

```php
Block::collapsible('Stack trace', Block::code($exception->getTraceAsString()));

Block::collapsible('Details', [
    Block::text('Some explanation'),
    Block::list(['One', 'Two']),
])->expanded($user->isAdmin());
```

##### Tabs

Shows one tab at a time, with the labels in a header bar. Pass `label => blocks` pairs, or `Block::tab()` instances when you need options. The first tab opens by default; mark another one with `active()`.

```php
ModalResponse::tabs([
    'Overview' => [
        Block::text('Everything went fine.'),
    ],
    Block::tab('Log', [
        Block::code($log)->withoutHighlighting(),
    ])->active(),
]);
```

`ModalResponse::tabs()` is shorthand for a stack with a single `Block::tabs([...])`.

#### Raw HTML

These render HTML as-is. See [Untrusted content](#untrusted-content) below before passing them anything a user controls.

##### HTML

Renders an HTML string as-is.

```php
Block::html('<p>Read the <a href="/docs">docs</a>.</p>');
```

##### Markdown

Compiles GitHub-flavored Markdown to HTML on the server (via Laravel's `Str::markdown()`). Call `file()` to treat the argument as a path to a Markdown file; an unreadable path throws an `InvalidArgumentException`.

```php
Block::markdown('**Bold** and a [link](https://nova.laravel.com).');
Block::markdown(resource_path('docs/release-notes.md'))->file();
```

##### View

Renders a Blade view on the server and shows the result as HTML.

```php
Block::view('reports.summary', ['report' => $report]);
```

### Untrusted content

`html`, `markdown` and `view` blocks are rendered without sanitization. Never pass user input, request data or other untrusted content to them without escaping or sanitizing it first. Use `text`, `list` or `code` for untrusted strings; those are always escaped.
