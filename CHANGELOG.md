# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-04-16

### Added

- Add language release tree sections and enhance translation management

## [1.0.2] - 2026-04-15

### Fixed

- Improve CHANGELOG.md update process in release script

### Maintenance

- Add CI configuration, testing setup, and initial test cases

## [1.0.0] - 2026-01-29

### Added
- Initial release
- ViewButton with checkbox interface for releasing language variants
- Frontend access control for unreleased language variants
- Three behavior options: 404, redirect, default-content
- Preview mode with token validation for authenticated users
- Configurable field name option
- Helper functions: `languageReleaseFieldName()`, `isLanguageReleased()`, `isAuthenticatedPreview()`
- Translations for DE, EN, ES, FR, IT, NL
- Comprehensive documentation

### Security
- Server-side token validation for preview mode
- Session-based authentication
- No bypass mechanisms for unauthenticated users


[1.1.0]: https://github.com/nerdcel/kirby-languagerelease/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/nerdcel/kirby-languagerelease/compare/v1.0.1...v1.0.2
[1.0.0]: https://github.com/nerdcel/kirby-languagerelease/releases/tag/v1.0.0
