<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('submitted_by');
            $table->string('title');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'quoted', 'accepted', 'declined'])->default('pending');
            $table->decimal('price_quoted', 10, 2)->nullable();
            $table->text('agency_response')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('submitted_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
