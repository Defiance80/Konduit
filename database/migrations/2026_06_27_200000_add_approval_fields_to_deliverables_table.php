<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->unsignedBigInteger('reviewer_id')->nullable()->after('client_id');
            $table->unsignedInteger('version')->default(1)->after('reviewer_id');
            $table->text('rejection_reason')->nullable()->after('client_feedback');
            $table->string('file_name')->nullable()->after('file_url');
            $table->string('file_mime')->nullable()->after('file_name');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_mime');

            $table->foreign('reviewer_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('deliverables', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['reviewer_id', 'version', 'rejection_reason', 'file_name', 'file_mime', 'file_size']);
        });
    }
};
