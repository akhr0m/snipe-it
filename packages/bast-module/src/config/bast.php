<?php

/**
 * BAST Module Configuration
 * 
 * Configuration for the BAST (Berita Acara Serah Terima) module.
 * This file can be published to config/bast.php for user customization.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable BAST Module
    |--------------------------------------------------------------------------
    |
    | Set this to false to completely disable BAST module functionality
    | without removing the code from the system.
    |
    */
    'enabled' => env('BAST_MODULE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Database Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for BAST-related database tables
    |
    */
    'table_prefix' => env('BAST_TABLE_PREFIX', 'bast_'),

    /*
    |--------------------------------------------------------------------------
    | BAST Report Number Format
    |--------------------------------------------------------------------------
    |
    | Format for generating BAST report numbers
    | Available placeholders: {sequence}, {department}, {year}, {month}
    |
    */
    'report_number_format' => env('BAST_REPORT_FORMAT', '{sequence}/BAST/IT/HO/{month}/{year}'),

    /*
    |--------------------------------------------------------------------------
    | Report Sequence Padding
    |--------------------------------------------------------------------------
    |
    | Number of digits for sequence padding (zero-padded)
    | Example: 5 = "00001", 3 = "001"
    |
    */
    'sequence_padding' => env('BAST_SEQUENCE_PADDING', 5),

    /*
    |--------------------------------------------------------------------------
    | BAST Report Settings
    |--------------------------------------------------------------------------
    |
    | Settings specific to BAST report generation and display
    |
    */
    'report_settings' => [
        /*
        | Include company/location address in BAST report
        */
        'include_address' => env('BAST_INCLUDE_ADDRESS', true),

        /*
        | Include company details in header
        */
        'include_company_details' => env('BAST_INCLUDE_COMPANY', true),

        /*
        | Auto-print report after generation
        */
        'auto_print' => env('BAST_AUTO_PRINT', false),

        /*
        | Show success message after saving BAST record
        */
        'show_save_message' => env('BAST_SHOW_SAVE_MSG', true),

        /*
        | Default language for BAST report (if multi-language support added)
        */
        'default_language' => env('BAST_DEFAULT_LANG', 'id'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for BAST module routes
    |
    */
    'routes' => [
        /*
        | Should BAST routes be loaded
        */
        'load_routes' => env('BAST_LOAD_ROUTES', true),

        /*
        | Route prefix for BAST endpoints
        */
        'prefix' => env('BAST_ROUTE_PREFIX', 'bast-report'),

        /*
        | Middleware to apply to BAST routes
        */
        'middleware' => ['auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Migrations
    |--------------------------------------------------------------------------
    |
    | Configuration for BAST database migrations
    |
    */
    'migrations' => [
        /*
        | Run BAST migrations automatically
        */
        'auto_run' => env('BAST_AUTO_MIGRATE', true),

        /*
        | BAST-specific migration path
        */
        'path' => env('BAST_MIGRATION_PATH', 'database/migrations/bast'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for BAST API endpoints
    |
    */
    'api' => [
        /*
        | Enable API endpoints
        */
        'enabled' => env('BAST_API_ENABLED', true),

        /*
        | API prefix
        */
        'prefix' => env('BAST_API_PREFIX', 'api/bast-report'),

        /*
        | API version
        */
        'version' => env('BAST_API_VERSION', 'v1'),

        /*
        | Middleware for API endpoints
        */
        'middleware' => ['auth:api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Caching settings for BAST reports
    |
    */
    'cache' => [
        /*
        | Enable caching for BAST reports
        */
        'enabled' => env('BAST_CACHE_ENABLED', false),

        /*
        | Cache TTL in seconds
        */
        'ttl' => env('BAST_CACHE_TTL', 3600),

        /*
        | Cache driver
        */
        'driver' => env('BAST_CACHE_DRIVER', 'file'),

        /*
        | Cache prefix
        */
        'prefix' => 'bast_report_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Individual feature toggles within BAST module
    |
    */
    'features' => [
        /*
        | Allow BAST report generation from user view
        */
        'allow_generation' => env('BAST_ALLOW_GENERATION', true),

        /*
        | Allow BAST report search/retrieval
        */
        'allow_search' => env('BAST_ALLOW_SEARCH', true),

        /*
        | Allow BAST report printing
        */
        'allow_printing' => env('BAST_ALLOW_PRINTING', true),

        /*
        | Allow BAST report deletion (if implemented)
        */
        'allow_deletion' => env('BAST_ALLOW_DELETION', false),

        /*
        | Show BAST menu in main navigation
        */
        'show_in_menu' => env('BAST_SHOW_IN_MENU', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notifications
    |--------------------------------------------------------------------------
    |
    | Settings for BAST-related email notifications
    |
    */
    'notifications' => [
        /*
        | Send email notification when BAST is generated
        */
        'notify_on_generation' => env('BAST_NOTIFY_ON_GENERATION', false),

        /*
        | Email recipients for BAST notifications
        */
        'recipients' => explode(',', env('BAST_NOTIFY_RECIPIENTS', '')),

        /*
        | Email notification channel
        */
        'channel' => env('BAST_NOTIFY_CHANNEL', 'mail'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit & Logging
    |--------------------------------------------------------------------------
    |
    | Settings for auditing and logging BAST operations
    |
    */
    'audit' => [
        /*
        | Log all BAST operations
        */
        'enabled' => env('BAST_AUDIT_ENABLED', true),

        /*
        | Log BAST report generation
        */
        'log_generation' => env('BAST_LOG_GENERATION', true),

        /*
        | Log BAST report access
        */
        'log_access' => env('BAST_LOG_ACCESS', true),

        /*
        | Log BAST report deletion
        */
        'log_deletion' => env('BAST_LOG_DELETION', true),
    ],
];
