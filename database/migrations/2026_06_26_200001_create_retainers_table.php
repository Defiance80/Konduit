<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retainers', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('client_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('monthly_value', 10, 2)->nullable();
            $table->unsignedInteger('hours_included')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'cancelled', 'completed'])->default('draft');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'annually'])->default('monthly');
            $table->json('services')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retainers');
    }
};
