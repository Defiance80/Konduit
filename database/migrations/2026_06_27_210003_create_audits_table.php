<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('conducted_by')->nullable();
            $table->string('title');
            $table->enum('type', ['seo', 'website', 'social', 'content', 'technical', 'performance', 'general'])->default('general');
            $table->enum('status', ['draft', 'in_progress', 'complete', 'shared'])->default('draft');
            $table->unsignedTinyInteger('score')->nullable();
            $table->json('findings')->nullable();
            $table->json('recommendations')->nullable();
            $table->text('ai_analysis')->nullable();
            $table->text('executive_summary')->nullable();
            $table->boolean('visible_to_client')->default(false);
            $table->date('audited_at')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('conducted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
