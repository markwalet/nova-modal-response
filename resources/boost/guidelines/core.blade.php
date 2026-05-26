## Nova Modal Response

`Markwalet\NovaModalResponse\ModalResponse` is a Laravel Nova `ActionResponse` that you return from a Nova action's `handle()` method. Instead of running a mutation, it opens a modal that displays content — a code snippet, a JSON payload, plain text, or raw HTML.

### When to use

Return a `ModalResponse` when the result of an action is something the user should **read or inspect** rather than a database change. Construct it with one of the static factories below and return it from `handle()`.

### When not to use

`ModalResponse` only displays content. For other outcomes, use Nova's built-in action responses instead:

- Downloading a file → `Action::download(...)`
- A short success/flash message → `Action::message(...)`
- Redirecting after a mutation → `Action::redirect(...)`

### Showing a code snippet

Use `ModalResponse::code()` to display a string of code. It is syntax-highlighted automatically.

@verbatim
<code-snippet name="Show a code snippet in a modal" lang="php">
public function handle(ActionFields $fields, Collection $models): ModalResponse
{
    return ModalResponse::code($generatedScript)
        ->title('Generated script');
}
</code-snippet>
@endverbatim

### Showing a JSON payload

Use `ModalResponse::json()` to display data as pretty-printed, syntax-highlighted JSON. It accepts an `array` (not a JSON string) and encodes it for you.

@verbatim
<code-snippet name="Show a JSON payload in a modal" lang="php">
public function handle(ActionFields $fields, Collection $models): ModalResponse
{
    return ModalResponse::json($models->first()->toArray())
        ->title('User payload');
}
</code-snippet>
@endverbatim

### Showing a block of plain text

Use `ModalResponse::text()` for a single short paragraph. The text is HTML-escaped. It renders inside one `<p>`, so newlines and extra whitespace are **not** preserved — for multi-line output use `code()` (preserves whitespace) or `html()` with explicit markup.

@verbatim
<code-snippet name="Show plain text in a modal" lang="php">
public function handle(ActionFields $fields, Collection $models): ModalResponse
{
    return ModalResponse::text('The export finished successfully.')
        ->title('Done');
}
</code-snippet>
@endverbatim

### Showing arbitrary HTML

Use `ModalResponse::html()` to render an HTML string verbatim.

**Warning:** `html()` renders the string with no sanitisation or escaping whatsoever. Never pass untrusted input (request data, user-provided content, AI output) directly — escape or vet it first.

@verbatim
<code-snippet name="Show raw HTML in a modal" lang="php">
public function handle(ActionFields $fields, Collection $models): ModalResponse
{
    return ModalResponse::html('<strong>Report ready.</strong> Check your inbox.')
        ->title('Report');
}
</code-snippet>
@endverbatim

### Modal chrome

Chain these fluent methods onto any `ModalResponse` to configure the modal frame:

- `->title(string $title)` — sets the modal's header title.
- `->size(string $size)` — sets the modal width. Defaults to `2xl`; accepts Nova's size tokens (e.g. `2xl`, `3xl`, … `7xl`).
- `->closeButton(string $label)` — sets the close button label. Defaults to `Close`.
- `->withoutSyntaxHighlighting()` — disables syntax highlighting for `code()` and `json()` modals.

### Reference

Runnable end-to-end examples live in `workbench/app/Nova/Actions/` in the package repository.
