<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('name');
            $table->string('color')->default('#6366f1');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('what_you_get')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('price_type', ['fixed', 'hourly', 'monthly', 'custom'])->default('fixed');
            $table->json('features')->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->enum('status', ['active', 'draft', 'archived'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('service_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
