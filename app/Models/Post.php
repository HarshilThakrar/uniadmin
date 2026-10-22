<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'cover_image_url',
        'author',
        'tags',
        'status',
        'source',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (Post $post) {
            if ($post->status === 'published' && empty($post->published_at)) {
                $post->published_at = now();
            }
        });
    }

    public function getTagsAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && !empty($value)) {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return array_values(array_filter(array_map('trim', explode(',', $value))));
        }

        return [];
    }

    public function getCoverImageUrlAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        // If it starts with / or is an internal frontend public asset
        if (str_starts_with($value, '/')) {
            return $value;
        }

        // Return full public URL for uploaded files in backend storage (e.g. posts/covers/xyz.jpg)
        if (request()->is('api/*') || !request()->is('admin/*')) {
            return asset('storage/' . ltrim($value, '/'));
        }

        return $value;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }
}
