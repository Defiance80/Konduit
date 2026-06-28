<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            if (!Schema::hasColumn('audits', 'website_url')) {
                $table->string('website_url', 500)->nullable()->after('type');
            }
            if (!Schema::hasColumn('audits', 'category_scores')) {
                $table->json('category_scores')->nullable()->after('score');
            }
            if (!Schema::hasColumn('audits', 'scan_data')) {
                $table->json('scan_data')->nullable()->after('category_scores');
            }
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropColumn(['website_url', 'category_scores', 'scan_data']);
        });
    }
};
