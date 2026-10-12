# Preview env

How the `implement-issues` skill spins up a local environment so a human can
review an implemented issue before the PR is opened. Edit this file to match the
project; the skill reads it at runtime.

## Target branch

`v2` — feature branches are cut from it and PRs target it. (Switch to `main` once
v2 is released.)

## Setup steps (before serving)

Run in order from the repo root.

```sh
npm install   # only if node_modules is missing
npm run prod  # compile resources/js -> dist (laravel-mix); REQUIRED to see Vue changes
```

The JS build is mandatory: Nova loads the compiled `dist/js/asset.js`, so a Vue
change (a new `*Block.vue`, the shared `blockComponents.js`, etc.) is invisible
until you rebuild. `node_modules` is usually present in the main checkout but
**not** in a fresh agent worktree — that's fine, the build happens here at review
time, not in the worktree.

Add migrate/seed/env/fixture steps below as the workbench grows, e.g.:

```sh
# cp .env.example .env        # if a workbench env is introduced
# php artisan migrate --seed  # if the workbench gains a DB
```

## Serve

Do **not** use `composer run serve -- --port <PORT>` — the `serve` script runs
`workbench:build` first and forwards `--port` to it, but `workbench:build` has no
`--port` option and errors out. Split the two steps:

```sh
composer run build                              # = testbench workbench:build
php vendor/bin/testbench serve --port <PORT>    # serve on the chosen port
```

The skill picks a random free high port for `<PORT>` and prints the
`http://127.0.0.1:<PORT>/nova` URL (the bare `/` 302-redirects into Nova).

## What to click

The workbench registers demo actions on the **`User`** Nova resource (e.g.
`ViewJsonSnippetModalAction`). Open a user, trigger the action(s) the issue
touches, and confirm the modal renders the new block/behaviour.
