# BAST Module for Snipe-IT

This package extracts the BAST (Berita Acara Serah Terima) functionality into a self-contained module that can be loaded by the application.

Installation (development within repository):

1. Ensure `feature/bast-module-extraction` branch is checked out.
2. Composer autoload is already configured in root composer.json for the `packages/` path.
3. Register the service provider (optional) in `config/app.php` or let the AppServiceProvider load it conditionally.

Usage

- Views are available under the `bast::` view namespace when the provider is loaded.
- Routes mirror the original route names for backward compatibility.

Configuration

Publishable config is copied from `config/bast.php`.

Testing

Module tests live under `packages/bast-module/tests/`.

