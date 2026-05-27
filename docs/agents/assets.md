# Assets

How agents should treat the compiled front-end assets in `dist/`.

## Don't compile assets in PRs

When working on a feature or fix, edit **only** the source in `resources/` (`resources/js`, `resources/css`). **Do not run `npm run prod` / `npm run dev` and do not stage or commit `dist/`.**

The compiled bundle is minified and regenerated wholesale on every build, so two branches that both rebuild it conflict on a blob git can't merge. Keeping `dist/` out of PRs makes feature branches conflict-free.

## Still run QA & tests

This does **not** mean skipping verification. Run the normal checks on your changes as usual:

```bash
vendor/bin/pint --test
vendor/bin/phpstan analyse --no-progress --memory-limit=1G
vendor/bin/phpunit
```

## Assets are built once, at release

`dist/` stays tracked in git — Composer/Nova consumers load it straight from the installed package (`src/AssetServiceProvider.php`), and there's no consumer-side build. It's regenerated and committed a single time per release via `composer release-build` (see `docs/RELEASING.md`), just before the tag is created. So between releases `dist/` on `main` may lag the source; that's expected and harmless, because every published tag carries a fresh build.
