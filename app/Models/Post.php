<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'published_at',
        'featured',
        'user_id',
        'thumbnail',
    ];

    public function scopePublished(Builder $query)
    {
        $query->where('published_at', '<=', Carbon::now());
    }

    public function scopeFeatured(Builder $query)
    {
        $query->where('featured', true);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getExcerpt()
    {
        return Str::limit(strip_tags($this->body),100,'...');
    }

    public function getReadingTime()
    {
        $mins = round(str_word_count($this->body) / 250);
        return ($mins < 1) ? 1 : $mins;
    }
}
