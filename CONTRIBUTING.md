# Contributing

Thanks for contributing! This is a short guide to get you started.

## Setup

This package depends on Laravel Nova, which is a paid product served from a private Composer repository, so you'll need Nova credentials to install:

```bash
composer config http-basic.nova.laravel.com "your-nova-email" "your-license-key"
composer install
npm install
```

## Making changes

- Work on a branch and open a pull request against `main`.
- Edit the **source** in `src/` (PHP) and `resources/` (JS/CSS).
- **Don't commit compiled assets.** Never run a production build or stage `dist/` in a PR — it's generated once per release, not per change. See [`docs/agents/assets.md`](docs/agents/assets.md) for why.
- Keep commit messages short and in the present tense ("Add badge variant"). Reference issues with `References #123` / `Closes #123` when relevant.

## Testing & QA

Run these before opening a PR — they're the same checks CI runs:

```bash
vendor/bin/pint --test     # code style (drop --test to auto-fix)
vendor/bin/phpstan analyse # static analysis
vendor/bin/phpunit         # test suite
```

To try your changes in a real Nova panel, serve the bundled workbench app:

```bash
composer serve
```

This boots the workbench server and `npm run watch` together, so PHP **and** Vue/CSS edits recompile live. The watcher writes gitignored dev builds, so serving never dirties the tracked `dist/` bundle.

## Releasing

Releases (including the asset build) are handled by maintainers — see [`docs/RELEASING.md`](docs/RELEASING.md).
