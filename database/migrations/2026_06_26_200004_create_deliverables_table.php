<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliverables', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('client_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_review', 'approved', 'rejected', 'delivered'])->default('pending');
            $table->string('file_path')->nullable();
            $table->string('file_url')->nullable();
            $table->text('client_feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliverables');
    }
};
