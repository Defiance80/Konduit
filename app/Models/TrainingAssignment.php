<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingAssignment extends Model
{
    protected $fillable = ['course_id', 'user_id', 'assigned_by', 'assigned_at'];

    protected $casts = ['assigned_at' => 'datetime'];

    public function course(): BelongsTo   { return $this->belongsTo(TrainingCourse::class, 'course_id'); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class, 'user_id'); }
    public function assigner(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }
}
