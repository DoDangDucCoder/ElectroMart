<?php

namespace App\Infrastructure\Persistence\Eloquent\Catalog;

use App\Infrastructure\Persistence\Eloquent\BaseModel;
use App\Infrastructure\Persistence\Eloquent\Customer\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ProductReview extends BaseModel
{
    protected $table = 'product_reviews';

    protected $casts = [
        'approved_at' => 'datetime',
        'meta' => 'array',
    ];

    protected $appends = [
        'comment_id',
        'comment_name',
        'comment',
        'comment_date',
        'comment_parent_comment',
        'comment_product_id',
        'comment_status',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getCommentIdAttribute(): int
    {
        return $this->id;
    }

    public function getCommentNameAttribute(): string
    {
        return $this->author_name
            ?? $this->customer?->name
            ?? 'Anonymous';
    }

    public function getCommentAttribute(): string
    {
        return $this->content ?? '';
    }

    public function getCommentDateAttribute(): string
    {
        $timestamp = $this->approved_at ?? $this->created_at;

        return $timestamp instanceof Carbon
            ? $timestamp->format('d/m/Y H:i')
            : '';
    }

    public function getCommentParentCommentAttribute(): ?int
    {
        return $this->parent_id;
    }

    public function getCommentProductIdAttribute(): int
    {
        return $this->product_id;
    }

    public function getCommentStatusAttribute(): int
    {
        return $this->status === 'approved' ? 0 : 1;
    }
}
