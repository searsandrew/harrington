<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("CREATE INDEX CONCURRENTLY IF NOT EXISTS posts_title_trgm_idx    ON posts USING gin (title gin_trgm_ops)");
        DB::statement("CREATE INDEX CONCURRENTLY IF NOT EXISTS posts_subtitle_trgm_idx ON posts USING gin (subtitle gin_trgm_ops)");
        DB::statement("CREATE INDEX CONCURRENTLY IF NOT EXISTS posts_content_trgm_idx  ON posts USING gin (content gin_trgm_ops)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS posts_title_trgm_idx");
        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS posts_subtitle_trgm_idx");
        DB::statement("DROP INDEX CONCURRENTLY IF EXISTS posts_content_trgm_idx");
    }
};
