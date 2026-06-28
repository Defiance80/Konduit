<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('address')->nullable()->after('website');
            $table->string('contact_person')->nullable()->after('address');
            $table->string('contact_person_email')->nullable()->after('contact_person');
            $table->string('contact_person_phone', 50)->nullable()->after('contact_person_email');
            $table->json('services_interested')->nullable()->after('contact_person_phone');
        });

        Schema::table('intake_submissions', function (Blueprint $table) {
            $table->string('address')->nullable()->after('company');
            $table->string('contact_person')->nullable()->after('address');
            $table->string('retainer_range', 50)->nullable()->after('contact_person');
            $table->text('project_goals')->nullable()->after('retainer_range');
            $table->json('services_interested')->nullable()->after('project_goals');
        });

        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('name');
            $table->string('file_path');
            $table->string('document_type', 30)->default('document');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_documents');
        Schema::table('intake_submissions', function (Blueprint $table) {
            $table->dropColumn(['address', 'contact_person', 'retainer_range', 'project_goals', 'services_interested']);
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['address', 'contact_person', 'contact_person_email', 'contact_person_phone', 'services_interested']);
        });
    }
};
