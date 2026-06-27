<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intake_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('website_url')->nullable();
            $table->string('issue_type')->default('general');
            $table->text('description');
            $table->string('priority')->default('medium');
            $table->json('ai_classification')->nullable();
            $table->text('ai_summary')->nullable();
            $table->text('ai_client_message')->nullable();
            $table->enum('status', ['received', 'processing', 'complete', 'failed'])->default('received');
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('project_templates', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('default_status')->default('draft');
            $table->unsignedSmallInteger('estimated_days')->nullable();
            $table->json('task_sections')->nullable(); // [{name, tasks:[{title, description, estimated_hours}]}]
            $table->json('deliverable_names')->nullable(); // array of deliverable titles to pre-create
            $table->boolean('is_shared')->default(false);
            $table->timestamps();
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_templates');
        Schema::dropIfExists('intake_submissions');
    }
};
