<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\TrainingAssignment;
use App\Models\TrainingCompletion;
use App\Models\TrainingCourse;
use App\Models\TrainingCurriculum;
use App\Models\TrainingLesson;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainingController extends Controller
{
    // ── Public (all agency users) ────────────────────────────────────────────

    public function index(): View
    {
        $tenantId = Auth::user()->tenant_id;
        $userId   = Auth::id();
        $isAdmin  = $this->isAdmin();

        $curricula = TrainingCurriculum::forTenant($tenantId)
            ->orderBy('sort_order')
            ->with(['courses' => function ($q) use ($tenantId) {
                $q->forTenant($tenantId)
                  ->where('is_published', true)
                  ->withCount('lessons')
                  ->orderBy('sort_order');
            }])
            ->get()
            ->each(function (TrainingCurriculum $cur) use ($userId) {
                $cur->courses->each(function (TrainingCourse $c) use ($userId) {
                    $c->user_progress  = $c->progressForUser($userId);
                    $c->is_assigned    = $c->isAssignedToUser($userId);
                });
            });

        $totalLessons     = TrainingLesson::whereIn('course_id',
            TrainingCourse::forTenant($tenantId)->pluck('id')
        )->count();

        $completedLessons = TrainingCompletion::where('user_id', $userId)->count();

        $overallProgress = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : 0;

        return view('agency.training.index', compact(
            'curricula', 'overallProgress', 'completedLessons', 'totalLessons', 'isAdmin'
        ));
    }

    public function show(TrainingCourse $trainingCourse): View
    {
        $userId    = Auth::id();
        $isAdmin   = $this->isAdmin();
        $tenantId  = Auth::user()->tenant_id;

        $lessons = $trainingCourse->lessons()->get()->each(function (TrainingLesson $l) use ($userId) {
            $l->is_completed = $l->isCompletedByUser($userId);
        });

        $progress    = $trainingCourse->progressForUser($userId);
        $assignments = $isAdmin
            ? TrainingAssignment::where('course_id', $trainingCourse->id)->with('user')->get()
            : collect();

        $teamMembers = $isAdmin
            ? User::where('tenant_id', $tenantId)->where('user_type', 'agency_user')->get()
            : collect();

        $assignedUserIds = $assignments->pluck('user_id')->toArray();

        return view('agency.training.show', compact(
            'trainingCourse', 'lessons', 'progress', 'isAdmin', 'assignments', 'teamMembers', 'assignedUserIds'
        ));
    }

    public function lesson(TrainingCourse $trainingCourse, TrainingLesson $trainingLesson): View
    {
        $userId      = Auth::id();
        $isCompleted = $trainingLesson->isCompletedByUser($userId);

        $lessons      = $trainingCourse->lessons()->get();
        $currentIndex = $lessons->search(fn ($l) => $l->id === $trainingLesson->id);
        $next         = $lessons[$currentIndex + 1] ?? null;
        $prev         = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $progress     = $trainingCourse->progressForUser($userId);

        return view('agency.training.lesson', compact(
            'trainingCourse', 'trainingLesson', 'isCompleted', 'next', 'prev', 'lessons', 'progress'
        ));
    }

    public function complete(TrainingCourse $trainingCourse, TrainingLesson $trainingLesson): RedirectResponse
    {
        TrainingCompletion::firstOrCreate([
            'user_id'   => Auth::id(),
            'course_id' => $trainingCourse->id,
            'lesson_id' => $trainingLesson->id,
        ], ['completed_at' => now()]);

        return redirect()
            ->route('agency.training.lesson', [$trainingCourse, $trainingLesson])
            ->with('success', 'Lesson marked as complete!');
    }

    // ── Admin only ───────────────────────────────────────────────────────────

    public function createCurriculum(): View
    {
        abort_unless($this->isAdmin(), 403);

        return view('agency.training.create-curriculum');
    }

    public function storeCurriculum(Request $request): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $data = $request->validate([
            'title'       => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|in:brand,success,warning,error,blue-light',
        ]);

        $data['tenant_id']   = Auth::user()->tenant_id;
        $data['sort_order']  = TrainingCurriculum::max('sort_order') + 1;

        $curriculum = TrainingCurriculum::create($data);

        return redirect()->route('agency.training.index')
            ->with('success', "Curriculum "{$curriculum->title}" created.");
    }

    public function createCourse(): View
    {
        abort_unless($this->isAdmin(), 403);

        $tenantId  = Auth::user()->tenant_id;
        $curricula = TrainingCurriculum::forTenant($tenantId)->orderBy('sort_order')->get();

        return view('agency.training.create-course', compact('curricula'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $data = $request->validate([
            'curriculum_id'     => 'nullable|exists:training_curricula,id',
            'title'             => 'required|string|max:200',
            'description'       => 'nullable|string|max:1000',
            'difficulty'        => 'required|in:beginner,intermediate,advanced',
            'estimated_minutes' => 'required|integer|min:1|max:480',
        ]);

        $data['tenant_id']   = Auth::user()->tenant_id;
        $data['is_published'] = true;
        $data['sort_order']  = TrainingCourse::max('sort_order') + 1;

        $course = TrainingCourse::create($data);

        return redirect()->route('agency.training.show', $course)
            ->with('success', 'Course created. Add lessons below.');
    }

    public function createLesson(TrainingCourse $trainingCourse): View
    {
        abort_unless($this->isAdmin(), 403);

        return view('agency.training.create-lesson', compact('trainingCourse'));
    }

    public function storeLesson(Request $request, TrainingCourse $trainingCourse): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $data = $request->validate([
            'title'            => 'required|string|max:200',
            'type'             => 'required|in:written,video',
            'content'          => 'nullable|string',
            'video_url'        => 'nullable|url|max:500',
            'duration_minutes' => 'required|integer|min:1|max:300',
        ]);

        if ($data['type'] === 'video' && !empty($data['video_url'])) {
            $data['video_provider'] = TrainingLesson::detectProvider($data['video_url']);
        }

        $data['course_id']  = $trainingCourse->id;
        $data['sort_order'] = $trainingCourse->lessons()->max('sort_order') + 1;

        TrainingLesson::create($data);

        return redirect()->route('agency.training.show', $trainingCourse)
            ->with('success', 'Lesson added.');
    }

    public function storeAssignment(Request $request, TrainingCourse $trainingCourse): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $request->validate([
            'user_ids'   => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->user_ids as $userId) {
            TrainingAssignment::firstOrCreate([
                'course_id' => $trainingCourse->id,
                'user_id'   => $userId,
            ], [
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);
        }

        return redirect()->route('agency.training.show', $trainingCourse)
            ->with('success', count($request->user_ids) . ' team member(s) assigned.');
    }

    public function removeAssignment(TrainingCourse $trainingCourse, User $user): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        TrainingAssignment::where('course_id', $trainingCourse->id)
            ->where('user_id', $user->id)
            ->delete();

        return redirect()->route('agency.training.show', $trainingCourse)
            ->with('success', 'Assignment removed.');
    }

    public function destroyCourse(TrainingCourse $trainingCourse): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);
        $trainingCourse->delete();

        return redirect()->route('agency.training.index')->with('success', 'Course deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function isAdmin(): bool
    {
        return Auth::user()->isSuperAdmin();
    }
}
