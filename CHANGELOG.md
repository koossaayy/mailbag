# Changelog

All notable changes to this project will be documented in this file.
This changelog was started in March 2025, so prior changes are not listed here.

## [2.0.1] - 2025-11-19

- Updated PHP dependencies.
- Updated from PHPUnit 11 to PHPUnit 12 for testing.

## [2.0.0] - 2025-03-04

- **Breaking Change:** Minimum PHP version changed to PHP 8.3.
- **Breaking Change:** Minimum node.js version changed to 22.
- Updated framework from Laravel 11 to Laravel 12.
- Updated Tailwind from v3 to v4.
- Updated workflows for codeberg/forgejo.
- Fixed duplication of contacts on lists.


## Migration from GitHub to Codeberg

To use the migrated & maintained Codeberg codebase you'll need to update the remote git location you pull from before attempting to update.
This can usually be done like so:

```bash
git remote set-url origin https://codeberg.org/danb/mailbag.git
```
