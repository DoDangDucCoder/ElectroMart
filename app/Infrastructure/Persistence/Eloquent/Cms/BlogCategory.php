<?php

namespace App\Infrastructure\Persistence\Eloquent\Cms;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends BaseModel
{
    protected $table = 'blog_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'cate_post_name',
        'cate_post_slug',
        'cate_post_desc',
        'cate_post_status',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'blog_category_id');
    }

    public function getCatePostIdAttribute(): int
    {
        return $this->id;
    }

    public function getCatePostNameAttribute(): ?string
    {
        return $this->name;
    }

    public function getCatePostSlugAttribute(): ?string
    {
        return $this->slug;
    }

    public function getCatePostDescAttribute(): ?string
    {
        return $this->description;
    }

    public function getCatePostStatusAttribute(): int
    {
        return $this->is_active ? 0 : 1;
    }
}
