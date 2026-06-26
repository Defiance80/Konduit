<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Single-database tenancy — stancl/tenancy is installed for future multi-db
 * migration but Phase 1 uses tenant_id column scoping only. All database
 * creation, migration, and context-switching events are intentionally disabled.
 */
class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {}
}
