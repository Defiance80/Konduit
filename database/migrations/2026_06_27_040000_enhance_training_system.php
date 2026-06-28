<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Curricula (top-level grouping: e.g. "Marketing Strategy")
        Schema::create('training_curricula', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('color')->default('brand');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add curriculum_id to courses
        Schema::table('training_courses', function (Blueprint $table) {
            $table->foreignId('curriculum_id')
                ->nullable()
                ->after('tenant_id')
                ->constrained('training_curricula')
                ->nullOnDelete();
        });

        // Add video support to lessons
        Schema::table('training_lessons', function (Blueprint $table) {
            $table->enum('type', ['written', 'video'])->default('written')->after('title');
            $table->string('video_provider')->nullable()->after('video_url');
        });

        // Assignment table (admin assigns a course to specific users)
        Schema::create('training_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('assigned_by');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            $table->unique(['course_id', 'user_id']);
            $table->foreign('assigned_by')->references('id')->on('users')->cascadeOnDelete();
        });

        $this->seedCurricula();
    }

    public function down(): void
    {
        Schema::dropIfExists('training_assignments');
        Schema::table('training_lessons', fn (Blueprint $t) => $t->dropColumn(['type', 'video_provider']));
        Schema::table('training_courses', fn (Blueprint $t) => $t->dropForeignIdFor(\App\Models\TrainingCurriculum::class));
        Schema::dropIfExists('training_curricula');
    }

    private function seedCurricula(): void
    {
        $now = now()->toDateTimeString();

        $curricula = [
            ['title' => 'Platform Training',   'description' => 'Get up to speed with Konduit and make the most of every feature.',          'color' => 'brand',      'old' => 'platform',   'sort_order' => 1],
            ['title' => 'Agency Operations',   'description' => 'Processes, capacity planning, and best practices for running your agency.', 'color' => 'blue-light', 'old' => 'agency_ops', 'sort_order' => 2],
            ['title' => 'Marketing Strategy',  'description' => 'SEO, paid advertising, content, and campaign fundamentals.',               'color' => 'success',    'old' => 'marketing',  'sort_order' => 3],
            ['title' => 'Client Management',   'description' => 'Communication, expectation-setting, and long-term retention.',             'color' => 'warning',    'old' => 'client_mgmt','sort_order' => 4],
            ['title' => 'AI & Tools',          'description' => 'AI-powered workflows, prompt engineering, and automation.',                 'color' => 'brand',      'old' => 'ai_tools',   'sort_order' => 5],
        ];

        foreach ($curricula as $c) {
            $old = $c['old'];
            unset($c['old']);
            $c['tenant_id']  = null;
            $c['created_at'] = $now;
            $c['updated_at'] = $now;

            $id = DB::table('training_curricula')->insertGetId($c);

            DB::table('training_courses')
                ->where('category', $old)
                ->update(['curriculum_id' => $id]);
        }
    }
};
