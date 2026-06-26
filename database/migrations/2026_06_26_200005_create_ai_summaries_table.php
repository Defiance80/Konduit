<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->morphs('summarizable');
            $table->string('type')->default('status');
            $table->text('content');
            $table->string('client_content')->nullable();
            $table->decimal('confidence', 4, 2)->nullable();
            $table->string('what_happened')->nullable();
            $table->string('why')->nullable();
            $table->string('what_next')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_summaries');
    }
};
