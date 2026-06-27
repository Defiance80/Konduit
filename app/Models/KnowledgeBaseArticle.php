<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KnowledgeBaseArticle extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'author_id', 'title', 'slug', 'excerpt',
        'content', 'category', 'is_public', 'published_at',
    ];

    protected $casts = [
        'is_public'    => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            if (empty($article->excerpt) && $article->content) {
                $article->excerpt = Str::limit(strip_tags($article->content), 160);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
