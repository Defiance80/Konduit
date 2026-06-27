<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_health_scores', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('engagement_score')->default(50);
            $table->unsignedTinyInteger('churn_risk_score')->default(50);
            $table->enum('churn_risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->json('factors')->nullable();
            $table->text('ai_notes')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'client_id']);
            $table->index('tenant_id');
        });

        Schema::create('sop_categories', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->string('color', 7)->default('#6366f1');
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::create('sops', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('sop_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('version', 20)->default('1.0');
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('knowledge_base_articles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('category')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'is_public']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_articles');
        Schema::dropIfExists('sops');
        Schema::dropIfExists('sop_categories');
        Schema::dropIfExists('client_health_scores');
    }
};
