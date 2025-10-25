<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPost extends BaseModel
{
    protected $table = 'blog_posts';

    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_path',
        'is_featured',
        'status',
        'published_at',
        'views_count',
        'meta_title',
        'meta_description',
        'meta',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'meta' => 'array',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'post_id',
        'cate_post_id',
        'post_title',
        'post_slug',
        'post_desc',
        'post_content',
        'post_meta_desc',
        'post_meta_keywords',
        'post_status',
        'post_image',
        'post_views',
        'featured_image_url',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function cate_post(): BelongsTo
    {
        return $this->category();
    }

    public function getPostIdAttribute(): int
    {
        return $this->id;
    }

    public function getCatePostIdAttribute(): ?int
    {
        return $this->blog_category_id;
    }

    public function getPostTitleAttribute(): ?string
    {
        return $this->title;
    }

    public function getPostSlugAttribute(): ?string
    {
        return $this->slug;
    }

    public function getPostDescAttribute(): ?string
    {
        return $this->excerpt;
    }

    public function getPostContentAttribute(): ?string
    {
        return $this->content;
    }

    public function getPostMetaDescAttribute(): ?string
    {
        return $this->meta_description ?? $this->excerpt;
    }

    public function getPostMetaKeywordsAttribute(): ?string
    {
        return $this->meta['keywords'] ?? null;
    }

    public function getPostStatusAttribute(): int
    {
        return match ($this->status) {
            'published' => 0,
            default => 1,
        };
    }

    public function getPostImageAttribute(): ?string
    {
        return $this->featured_image_path ? basename($this->featured_image_path) : null;
    }

    public function getPostViewsAttribute(): int
    {
        return $this->views_count ?? 0;
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image_path ? url($this->featured_image_path) : null;
    }
}
