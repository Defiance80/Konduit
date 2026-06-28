<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingLesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'content', 'video_url', 'duration_minutes', 'sort_order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    public function isCompletedByUser(int $userId): bool
    {
        return TrainingCompletion::where('lesson_id', $this->id)
            ->where('user_id', $userId)
            ->exists();
    }
}
