# Changelog

All notable changes to `epitre` will be documented in this file.

## 1.0.1 - 2026-03-17

### Added
- `navigationGroup()` method on `EpitrePlugin` to place the Email Templates resource inside a named navigation group (e.g. Settings)

## 1.0.0 - 2026-03-17

### Added
- Register email templates via `Epitre::register()` and manage subject/body through a Filament resource
- Optional `$layout` property on `EpitreTemplate` to wrap stored content in a Blade mail layout
- Token system with `$tokens` array and `resolve()` method for dynamic content substitution
- `epitre:make-template` Artisan command to scaffold template class, Blade view, and shared layout stub
- Filament v5 resource with edit form, status badges, and reset-to-default action
