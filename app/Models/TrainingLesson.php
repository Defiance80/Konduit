<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingLesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'type', 'content', 'video_url', 'video_provider', 'duration_minutes', 'sort_order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(TrainingCourse::class, 'course_id');
    }

    public function isCompletedByUser(int $userId): bool
    {
        return TrainingCompletion::where('lesson_id', $this->id)->where('user_id', $userId)->exists();
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->type !== 'video' || !$this->video_url) {
            return null;
        }

        return match ($this->video_provider) {
            'youtube' => $this->youtubeEmbed(),
            'vimeo'   => $this->vimeoEmbed(),
            default   => $this->video_url,
        };
    }

    public static function detectProvider(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }
        if (str_contains($url, 'vimeo.com')) {
            return 'vimeo';
        }

        return 'other';
    }

    private function youtubeEmbed(): string
    {
        foreach ([
            '/[?&]v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/embed\/([a-zA-Z0-9_-]{11})/',
        ] as $pattern) {
            if (preg_match($pattern, $this->video_url, $m)) {
                return "https://www.youtube.com/embed/{$m[1]}?rel=0";
            }
        }

        return $this->video_url;
    }

    private function vimeoEmbed(): string
    {
        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}?title=0&byline=0";
        }

        return $this->video_url;
    }
}
