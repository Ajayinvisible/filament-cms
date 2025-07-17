<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    public function scopePublished(Builder $query)
    {
        $query->where('published_at', '<=', Carbon::now());
    }

    public function scopeFeatured(Builder $query)
    {
        $query->where('featured', true);
    }
}
