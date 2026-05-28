# Release Notes

## [Unreleased](https://github.com/markwalet/nova-modal-response/compare/v1.2.0...main)

### Changed
- `composer serve` now runs `npm run watch` alongside the workbench server, so PHP and Vue/CSS changes recompile live
- Dev asset builds are isolated from the shipped bundle: `npm run watch`/`dev` emit gitignored unminified files, and only `npm run prod` writes the tracked minified bundle (`dist/**/asset.min.*`)
- Agent documentation moved from a Boost first-party guideline (always loaded into consumers' `CLAUDE.md`) to an on-demand Boost first-party skill (`nova-modal-response-development`)

## [v1.2.0 (2026-05-27)](https://github.com/markwalet/nova-modal-response/compare/v1.1.3...v1.2.0)

### Added
- Added a copy-to-clipboard button to code and json modal content ([#76](https://github.com/markwalet/nova-modal-response/issues/76))

## [v1.1.3 (2026-05-27)](https://github.com/markwalet/nova-modal-response/compare/v1.1.2...v1.1.3)

### Added
- `ModalResponse::code()` and `ModalResponse::json()` accept a `highlight` argument to toggle syntax highlighting at construction (e.g. `ModalResponse::code($snippet, highlight: false)`)

### Deprecated
- `ModalResponse::withoutSyntaxHighlighting()` — pass `highlight: false` to `code()`/`json()` instead. Removed in v2.

## [v1.1.2 (2026-05-27)](https://github.com/markwalet/nova-modal-response/compare/v1.1.1...v1.1.2)

### Added
- Added a contributing guide and a release process doc
- Added a `composer release-build` script that runs QA and compiles assets for a release

### Changed
- Compiled assets in `dist/` are now built once per release instead of in every PR

## [v1.1.1 (2026-05-26)](https://github.com/markwalet/nova-modal-response/compare/v1.1.0...v1.1.1)

### Added
- Added a Laravel Boost AI guideline so agents can use the package correctly ([#43](https://github.com/markwalet/nova-modal-response/issues/43))

### Changed
- Removed the raw `Action::modal('modal-response', [...])` pattern from the docs in favour of the `ModalResponse` helper ([#41](https://github.com/markwalet/nova-modal-response/issues/41))

## [v1.1.0 (2026-03-30)](https://github.com/markwalet/nova-modal-response/compare/v1.0.3...v1.1.0)

### Added
- Included Pint & PHPStan static analysis
- Added a GitHub Actions workflow for static analysis
- Update dependabot configuration
- Added a PHPUnit and Testbench-based package test suite
- Added a GitHub Actions test matrix for supported PHP 8.2+ and Laravel 12/13 versions

### Changed
- Raised the minimum supported PHP version to 8.2
- Raised the minimum supported Laravel version to 12

## [v1.0.3 (2026-03-05)](https://github.com/markwalet/nova-modal-response/compare/v1.0.2...v1.0.3)

### Added
- Added Laravel 13 support

### Removed
- Revoked and removed leaked api token for `laravel/nova` dependency

## [v1.0.2 (2025-04-08)](https://github.com/markwalet/nova-modal-response/compare/v1.0.1...v1.0.2)

### Fixed
- Make sure the input helper passes through all configuration options (https://github.com/markwalet/nova-modal-response/issues/13)

## [v1.0.1 (2024-12-20)](https://github.com/markwalet/nova-modal-response/compare/v1.0.0...v1.0.1)

### Added
- Added Laravel 12 support

## [v1.0.0 (2024-12-20)](https://github.com/markwalet/nova-modal-response/compare/v0.3.0...v1.0.0)

### Added
- Close modal on escape
- Added option to configure close button text
- Added optional syntax highlighting for code (enabled by default)
- Added dark mode support
- Installed Nova devtools
- Added an input helper with sensible defaults (`ModalResponse`)

### Changed
- Moved to new webpack mix configuration
- Made code block the full width of a modal
- Changed default close button text to `Close` instead of `Cancel`

### Removed
- Removed support for `laravel/nova` V4

## [v0.3.1 (2024-12-16)](https://github.com/markwalet/nova-modal-response/compare/v0.3.0...v0.3.1)

### Added
- Added support for `laravel/nova:^5.0`.

### Removed
- Removed support for Laravel 8 & 9

## [v0.3.0 (2024-03-13)](https://github.com/markwalet/nova-modal-response/compare/v0.2.2...v0.3.0)

### Added
- Added Laravel 11 support

## [v0.2.2 (2024-02-27)](https://github.com/markwalet/nova-modal-response/compare/v0.2.1...v0.2.2)

### Fixed
- Recompile assets to fix typo

## [v0.2.1 (2024-01-31)](https://github.com/markwalet/nova-modal-response/compare/v0.2.0...v0.2.1)

### Added
- Make size configurable for modal

### Changed
- Make nova version requirement more precise

## [v0.2.0 (2023-03-02)](https://github.com/markwalet/nova-modal-response/compare/v0.1.4...v0.2.0)

### Added
- Added Laravel 10 support
- Added missing documentation for new features ([#1](https://github.com/markwalet/nova-modal-response/issues/1))
- Added a changelog

## [v0.1.4 (2023-02-27)](https://github.com/markwalet/nova-modal-response/compare/v0.1.3...v0.1.4)

### Fixed
- Improved styling for code block displays

## [v0.1.3 (2023-02-27)](https://github.com/markwalet/nova-modal-response/compare/v0.1.2...v0.1.3)

### Added
- Added a way to show preformatted code

## [v0.1.2 (2023-02-27)](https://github.com/markwalet/nova-modal-response/compare/v0.1.1...v0.1.2)

### Added
- Added a way to render unescaped text

## [v0.1.1 (2023-02-27)](https://github.com/markwalet/nova-modal-response/compare/v0.1.0...v0.1.1)

### Fixed
- Fixed inconsistencies in documentation

## v0.1.0 (2023-02-27)

Initial release
