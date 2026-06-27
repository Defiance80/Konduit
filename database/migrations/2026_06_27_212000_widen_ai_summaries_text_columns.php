<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_summaries', function (Blueprint $table) {
            $table->text('content')->change();
            $table->text('client_content')->nullable()->change();
            $table->text('what_happened')->nullable()->change();
            $table->text('why')->nullable()->change();
            $table->text('what_next')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ai_summaries', function (Blueprint $table) {
            $table->string('content')->change();
            $table->string('client_content')->nullable()->change();
            $table->string('what_happened')->nullable()->change();
            $table->string('why')->nullable()->change();
            $table->string('what_next')->nullable()->change();
        });
    }
};
