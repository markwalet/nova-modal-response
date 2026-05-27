# Dev asset builds isolated from the shipped bundle by filename

## Context

The compiled front-end bundle (`dist/js/asset.js`, `dist/css/asset.css`) is tracked in git and loaded straight from the installed package by `AssetServiceProvider` — there is no consumer-side build step. It is rebuilt and committed exactly once per release via `composer release-build`.

Iterating locally means running `npm run watch` (laravel-mix) so Vue/CSS changes recompile into `dist/`. But watch wrote to the *same* files as the shipped production bundle, so every local serve dirtied the tracked `dist/`. Keeping those changes out of PRs was a manual discipline ("don't compile assets in PRs") that was easy to forget, and we wanted `composer serve` to run the watcher automatically — which would have made the dirtying happen on every serve.

## Decision

Dev and production builds emit **different filenames** in the one `dist/` directory, and only the production names are tracked:

- **Production** (`npm run prod`, i.e. `mix --production`) emits `dist/js/asset.min.js` and `dist/css/asset.min.css`. These are the tracked, shipped bundle.
- **Dev/watch** (`npm run dev` / `npm run watch`) emits unminified `dist/js/asset.js` and `dist/css/asset.css`. These are **gitignored**, alongside the regenerated `dist/mix-manifest.json` and terser's `*.LICENSE.txt` sidecar.
- `webpack.mix.js` switches the output filename on `mix.inProduction()`.
- `AssetServiceProvider` resolves which bundle to register through `dist/mix-manifest.json`, which laravel-mix rewrites on every build to name that build's output. When the manifest is present it registers the assets it lists via `Nova::mix()`; when it's absent it falls back to the shipped `asset.min.*`. Because the manifest is gitignored, a consumer install never has one and always gets the minified bundle, while a local checkout serves whatever it built **last** — `npm run prod` wins even with a stale unminified `asset.js` on disk, and `npm run watch` wins while it runs.

## Considered alternatives

- **Status quo — keep one filename, rely on manual discipline.** Rejected: it can't be automated into `composer serve` without dirtying `dist/` on every serve, and "remember not to commit `dist/`" is exactly the kind of rule that leaks compiled blobs into PRs and causes unmergeable conflicts.
- **A separate gitignored directory for dev output (e.g. `dist-dev/`).** Rejected: a second top-level build directory for the same asset, and `Nova::script()` appends rather than dedupes by name (`InteractsWithAssets`), so the served path still has to be chosen inside `AssetServiceProvider` — the directory split bought nothing over the filename split and added clutter.
- **Register the dev bundle from the workbench's Nova provider instead of touching the shipped provider.** Rejected for the same append behaviour: registering a second script under the same name loads *both* bundles, double-registering every Vue component.
- **Resolve per-file with `is_file()` — prefer `asset.js` if present, else `asset.min.js`.** Rejected: simpler, but a stale unminified `asset.js` from an earlier `watch` masks a freshly built production bundle, so `npm run prod` wouldn't take effect locally until the dev files were manually deleted. Reading the manifest means the last build always wins.

## Consequences

- Dev builds physically cannot touch the tracked bundle, so `git status` stays clean no matter how long `composer serve` runs. The "don't compile assets in PRs" rule is now enforced by `.gitignore` rather than memory — only a deliberate `npm run prod` writes to a tracked file.
- The shipped filenames change from `asset.{js,css}` to `asset.min.{js,css}`. The existing tracked files were already production builds, so the migration is a rename (`git mv`), not a rebuild.
- `dist/mix-manifest.json` is no longer tracked, but it *is* read locally: `AssetServiceProvider` calls `Nova::mix()` whenever the manifest is present. Its absence in a consumer install (it's gitignored, never shipped) is the signal to fall back to the shipped `asset.min.*`.
- The last build always wins locally — `npm run prod` is served even if a stale unminified `asset.js` is still on disk, so there's nothing to remember to delete.
- `Nova::mix()` registers each asset under a content-hashed handle (`nova-modal-response-<hash>`), so the internal asset name differs from the bare `nova-modal-response` used on the consumer fallback path. This is only Nova's internal asset handle (a URL segment) and has no functional effect.
