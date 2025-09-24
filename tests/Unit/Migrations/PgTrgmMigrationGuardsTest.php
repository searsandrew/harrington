<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('pg_trgm extension migration is a no-op on non-pgsql', function () {
    expect(Schema::getConnection()->getDriverName())->not()->toBe('pgsql');

    $migration = require database_path('migrations/2025_09_24_190519_enable_pg_trgm.php');

    // Should not throw on up/down when not using pgsql
    $migration->up();
    $migration->down();

    expect(true)->toBeTrue();
});

it('trigram index migration is a no-op on non-pgsql', function () {
    expect(Schema::getConnection()->getDriverName())->not()->toBe('pgsql');

    $migration = require database_path('migrations/2025_09_24_190608_add_trgm_indexes_to_posts_table.php');

    $migration->up();
    $migration->down();

    expect(true)->toBeTrue();
});
