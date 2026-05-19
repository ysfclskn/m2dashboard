<?php

namespace App\Models;

use App\Enums\WikiPageCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WikiPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id', 'created_by', 'updated_by', 'title', 'slug', 'content', 'category',
    ];

    protected function casts(): array
    {
        return [
            'category' => WikiPageCategory::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (WikiPage $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
