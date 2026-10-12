# Releasing

The compiled assets in `dist/` are **not** built in PRs (see `docs/agents/assets.md`). They're built once, here, as part of cutting a release. Composer never builds anything on the consumer's machine — `src/AssetServiceProvider.php` loads `dist/js/asset.js` and `dist/css/asset.css` straight out of the installed package — so the freshly built `dist/` must be committed and pushed **before** the release tag is created.

## Steps

From an up-to-date, clean `main`:

1. **Build & verify** — runs Pint, PHPStan, the test suite, then compiles the production assets:

   ```bash
   composer release-build
   ```

   If any check fails, stop and fix it before continuing.

2. **Refresh the screenshots** (when the modal's look changed) — regenerates the README images in `art/` from the workbench, each showing light and dark mode split diagonally:

   ```bash
   npx playwright install chromium   # once per machine
   npm run screenshots
   ```

   The scenes are the `ScreenshotAction` modals in `workbench/app/Nova/Actions/ScreenshotAction.php`. Check the images before committing.

3. **Update the changelog** — move the `Unreleased` items under a new dated version section in `CHANGELOG.md` and refresh the compare links. (`/prepare-changelog` automates this.)

4. **Commit & push** — one commit with the built assets, screenshots and the changelog:

   ```bash
   git add CHANGELOG.md dist art
   git commit -m "Prepare vX.Y.Z release"
   git push
   ```

5. **Publish** — create the release in the GitHub Releases UI:
   - Target: `main` (must include the commit from step 4).
   - Tag: `vX.Y.Z`.
   - Notes: the changelog section from step 3.

## Why the order matters

The GitHub Releases UI creates the tag from `main`'s current HEAD at publish time. Publish **after** step 4 is pushed, so the tag captures the freshly built `dist/`. A build done after publishing lands too late to be in the tag, producing a release whose assets don't exist.
