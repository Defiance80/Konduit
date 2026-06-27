<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_summaries', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('type');
            $table->boolean('visible_to_client')->default(false)->after('client_content');
            $table->string('generated_by')->default('claude-sonnet-4-6')->after('visible_to_client');
        });
    }

    public function down(): void
    {
        Schema::table('ai_summaries', function (Blueprint $table) {
            $table->dropColumn(['subject', 'visible_to_client', 'generated_by']);
        });
    }
};
