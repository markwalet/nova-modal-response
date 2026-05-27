# Assets

How agents should treat the compiled front-end assets in `dist/`.

## Don't compile the shipped bundle in PRs

When working on a feature or fix, edit **only** the source in `resources/` (`resources/js`, `resources/css`). **Do not run `npm run prod`** — that's the one command that writes the tracked, shipped bundle.

The shipped bundle is minified and regenerated wholesale on every production build, so two branches that both rebuild it conflict on a blob git can't merge. Keeping it out of PRs makes feature branches conflict-free.

Dev builds are safe by construction: `npm run dev`/`watch` (and therefore `composer serve`) emit **gitignored** unminified files (`dist/js/asset.js`, `dist/css/asset.css`), while only `npm run prod` writes the tracked `dist/**/asset.min.*`. So you can serve and watch freely without dirtying `dist/`. See [`docs/adr/0005-dev-asset-builds-isolated-by-filename.md`](../adr/0005-dev-asset-builds-isolated-by-filename.md).

## Still run QA & tests

This does **not** mean skipping verification. Run the normal checks on your changes as usual:

```bash
vendor/bin/pint --test
vendor/bin/phpstan analyse --no-progress --memory-limit=1G
vendor/bin/phpunit
```

## Assets are built once, at release

The minified bundle (`dist/**/asset.min.*`) stays tracked in git — Composer/Nova consumers load it straight from the installed package (`src/AssetServiceProvider.php`), and there's no consumer-side build. It's regenerated and committed a single time per release via `composer release-build` (see `docs/RELEASING.md`), just before the tag is created. So between releases `dist/` on `main` may lag the source; that's expected and harmless, because every published tag carries a fresh build.
