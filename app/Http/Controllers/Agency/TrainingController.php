<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\TrainingCompletion;
use App\Models\TrainingCourse;
use App\Models\TrainingLesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(): View
    {
        $tenantId = Auth::user()->tenant_id;
        $userId   = Auth::id();

        $courses = TrainingCourse::forTenant($tenantId)
            ->where('is_published', true)
            ->withCount('lessons')
            ->orderBy('sort_order')
            ->get()
            ->each(function (TrainingCourse $c) use ($userId) {
                $c->user_progress = $c->progressForUser($userId);
            });

        $totalLessons    = TrainingLesson::whereIn('course_id', $courses->pluck('id'))->count();
        $completedLessons = TrainingCompletion::where('user_id', $userId)
            ->whereIn('course_id', $courses->pluck('id'))
            ->count();

        $overallProgress = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : 0;

        $grouped = $courses->groupBy('category');

        $categoryOrder = ['platform', 'agency_ops', 'marketing', 'client_mgmt', 'ai_tools'];
        $grouped = collect($categoryOrder)
            ->filter(fn ($k) => $grouped->has($k))
            ->mapWithKeys(fn ($k) => [$k => $grouped[$k]])
            ->merge($grouped->except($categoryOrder));

        return view('agency.training.index', compact(
            'courses', 'grouped', 'overallProgress', 'completedLessons', 'totalLessons'
        ));
    }

    public function show(TrainingCourse $trainingCourse): View
    {
        $userId  = Auth::id();
        $lessons = $trainingCourse->lessons()->get()->each(function (TrainingLesson $l) use ($userId) {
            $l->is_completed = $l->isCompletedByUser($userId);
        });
        $progress = $trainingCourse->progressForUser($userId);

        return view('agency.training.show', compact('trainingCourse', 'lessons', 'progress'));
    }

    public function lesson(TrainingCourse $trainingCourse, TrainingLesson $trainingLesson): View
    {
        $userId      = Auth::id();
        $isCompleted = $trainingLesson->isCompletedByUser($userId);

        $lessons      = $trainingCourse->lessons()->get();
        $currentIndex = $lessons->search(fn ($l) => $l->id === $trainingLesson->id);
        $next         = $lessons[$currentIndex + 1] ?? null;
        $prev         = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;

        $progress = $trainingCourse->progressForUser($userId);

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
        ], [
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('agency.training.lesson', [$trainingCourse, $trainingLesson])
            ->with('success', 'Lesson marked as complete!');
    }
}
