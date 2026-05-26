# Preview env

How the `implement-issues` skill spins up a local environment so a human can
review an implemented issue before the PR is opened. Edit this file to match the
project; the skill reads it at runtime.

## Target branch

`v2` — feature branches are cut from it and PRs target it. (Switch to `main` once
v2 is released.)

## Setup steps (before serving)

Run in order from the repo root. None required today beyond the build that
`serve` already does. Add migrate/seed/env/fixture steps here as the workbench
grows, e.g.:

```sh
# (none yet)
# cp .env.example .env        # if a workbench env is introduced
# php artisan migrate --seed  # if the workbench gains a DB
```

## Serve

```sh
composer run serve -- --port <PORT>
```

`composer run serve` runs `workbench:build` then `testbench serve`. The skill
picks a random free high port for `<PORT>` and prints the `http://127.0.0.1:<PORT>`
URL.

## What to click

The workbench registers demo actions on the **`User`** Nova resource (e.g.
`ViewJsonSnippetModalAction`). Open a user, trigger the action(s) the issue
touches, and confirm the modal renders the new block/behaviour.
