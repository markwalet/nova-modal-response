# Releasing

The compiled assets in `dist/` are **not** built in PRs (see `docs/agents/assets.md`). They're built once, here, as part of cutting a release. Composer never builds anything on the consumer's machine — `src/AssetServiceProvider.php` loads `dist/js/asset.js` and `dist/css/asset.css` straight out of the installed package — so the freshly built `dist/` must be committed and pushed **before** the release tag is created.

## Steps

From an up-to-date, clean `main`:

1. **Build & verify** — runs Pint, PHPStan, the test suite, then compiles the production assets:

   ```bash
   composer release-build
   ```

   If any check fails, stop and fix it before continuing.

2. **Update the changelog** — move the `Unreleased` items under a new dated version section in `CHANGELOG.md` and refresh the compare links. (`/prepare-changelog` automates this.)

3. **Commit & push** — one commit with the built assets and the changelog:

   ```bash
   git add CHANGELOG.md dist
   git commit -m "Prepare vX.Y.Z release"
   git push
   ```

4. **Publish** — create the release in the GitHub Releases UI:
   - Target: `main` (must include the commit from step 3).
   - Tag: `vX.Y.Z`.
   - Notes: the changelog section from step 2.

## Why the order matters

The GitHub Releases UI creates the tag from `main`'s current HEAD at publish time. Publish **after** step 3 is pushed, so the tag captures the freshly built `dist/`. A build done after publishing lands too late to be in the tag, producing a release whose assets don't exist.
