# BAST Module Extraction - Implementation Guide

## Overview
This guide documents the step-by-step process of extracting the BAST (Berita Acara Serah Terima) feature from Snipe-IT core to a fully isolated, modular package that can coexist with upstream updates without conflicts.

## Current Status
- **Branch**: `feature/bast-module-extraction`
- **Created**: 2026-06-02
- **Base**: v1.1.3

## Phase 1: Service Layer Extraction ✅
Created `app/Services/BastReportService.php` that encapsulates all BAST business logic:
- Report generation and numbering
- Report storage and retrieval
- Data formatting for views and APIs
- User snapshot management
- Header snapshot management

**Why**: This service will be moved to the module later, separating concerns from the controller.

## Phase 2: Configuration Extraction ✅
Created `config/bast.php` with:
- Enable/disable toggle
- Report numbering format
- Settings for features
- API configuration
- Cache settings
- Migration auto-run option

**Why**: Allows end-users to configure BAST without modifying core files.

## Phase 3: Module Structure (NEXT STEPS)
Will create modular package at `packages/bast-module/`:

```
packages/bast-module/
├── src/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── BastReportController.php
│   │   └── Routes/
│   │       └── routes.php
│   ├── Models/
│   │   └── UserReport.php
│   ├── Services/
│   │   └── BastReportService.php
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2025_09_17_024800_create_user_reports_table.php
│   │   │   ├── 2025_09_17_100103_modify_user_reports_for_logging.php
│   │   │   └── 2026_04_13_170000_add_snapshots_to_user_reports_table.php
│   │   └── Factories/
│   │       └── UserReportFactory.php
│   ├── Views/
│   │   └── reports/
│   │       ├── bast.blade.php
│   │       └── find-bast.blade.php
│   ├── Providers/
│   │   └── BastModuleServiceProvider.php
│   └── config/
│       └── bast.php
├── composer.json
├── README.md
└── LICENSE
```

## Phase 4: Controller Refactoring (NEXT STEPS)
The `app/Http/Controllers/Users/UsersController.php` will be refactored to:
1. Remove BAST-specific methods (lines 77-939)
2. Keep only facade/wrapper methods that delegate to BastReportService
3. Minimize modifications to core controller

**Example refactored method**:
```php
public function getBastReport(User $user)
{
    // Simply delegate to service
    return view('reports.bast', app(BastReportService::class)->generateBastPreview($user));
}
```

## Phase 5: Route Registration (NEXT STEPS)
Routes will be:
1. Moved to `packages/bast-module/src/Http/Routes/routes.php`
2. Auto-registered via ServiceProvider if module is enabled
3. Keeps original route names for backward compatibility
4. No changes needed to core `routes/web.php`

## Phase 6: UI Integration (NEXT STEPS)
The "Preview BAST" button in `resources/views/users/view.blade.php` will:
1. Check if BAST module is enabled
2. Show/hide based on configuration
3. Still use same route name (for backward compatibility)

## Migration Strategy for Upstream Merges

### Before Merge
```bash
# Keep feature/bast-module-extraction up-to-date
git fetch upstream
git rebase upstream/v1.1.3
```

### Merge Process
1. **Core changes are minimal** (only service registration in AppServiceProvider)
2. **Module is in /packages** (completely isolated)
3. **Config is published** (user-controlled)
4. **Routes are conditionally loaded** (via provider)

### Zero-Conflict Result
✅ No conflicts with upstream UserController changes
✅ No conflicts with route definitions
✅ No conflicts with migrations (separate package)
✅ Easy rollback if needed (just disable in config)

## Configuration File Locations

### Shipped with Module
- `packages/bast-module/config/bast.php`

### Published to Application
```bash
php artisan vendor:publish --provider="BastModule\Providers\BastModuleServiceProvider" --tag=config
# Creates: config/bast.php
```

## Enabling/Disabling Module

### Via .env
```bash
BAST_MODULE_ENABLED=true
```

### Via Config
```php
// config/bast.php
'enabled' => env('BAST_MODULE_ENABLED', true),
```

### Via Code
```php
if (config('bast.enabled')) {
    // BAST features available
}
```

## Testing Strategy

### Unit Tests
- `tests/Unit/Services/BastReportServiceTest.php`
- Test report number generation
- Test data formatting
- Test retrievals

### Feature Tests
- `tests/Feature/Bast/BastReportGenerationTest.php`
- Test full report flow
- Test API endpoints
- Test UI integration

### Integration Tests
- Test with upstream version
- Test enable/disable toggle
- Test migration compatibility

## Files Modified vs Created

### New Files Created
✅ `app/Services/BastReportService.php` (service layer)
✅ `config/bast.php` (configuration)
✅ `BAST_MODULE_EXTRACTION.md` (this file)

### Files to Modify Later
⏳ `app/Http/Controllers/Users/UsersController.php` (methods refactored to use service)
⏳ `app/Providers/AppServiceProvider.php` (register service if enabled)
⏳ `.gitignore` (add /packages/bast-module to tracked paths)

### Files to Move to Module
⏳ `app/Models/UserReport.php`
⏳ `database/migrations/*bast*`
⏳ `resources/views/reports/bast.blade.php`
⏳ `resources/views/reports/find-bast.blade.php`
⏳ `routes/web.php` (BAST routes only)
⏳ `tests/Feature/Users/Ui/BastReport*.php`

## Rollback Plan

If module extraction causes issues, rollback is simple:
1. Checkout `v1.1.3` branch
2. All BAST functionality remains intact
3. Module extraction branch can be revised and retried

## Next Steps

1. **Phase 3**: Create full module structure in `/packages/bast-module/`
2. **Phase 4**: Refactor controller to use service
3. **Phase 5**: Implement ServiceProvider for auto-registration
4. **Phase 6**: Create facade for easy access
5. **Phase 7**: Update composer.json autoloading
6. **Phase 8**: Create comprehensive documentation
7. **Phase 9**: Test with upstream merge scenario
8. **Phase 10**: Merge feature branch to v1.1.3

## Notes

- This extraction ensures ZERO conflicts with grokability/snipe-it upstream
- Module can be disabled/enabled without code changes (config only)
- Easy to maintain, test, and extend
- Compatible with both manual and automated deployments
- Backward compatible with existing code

---

**Status**: In Progress  
**Last Updated**: 2026-06-02  
**Assigned To**: akhr0m
