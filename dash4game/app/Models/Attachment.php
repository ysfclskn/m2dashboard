<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'project_id', 'uploaded_by', 'attachable_type', 'attachable_id',
        'original_name', 'file_name', 'file_path', 'mime_type', 'size',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return asset($this->file_path);
    }

    public function formattedSize(): string
    {
        $kb = $this->size / 1024;
        return $kb < 1024
            ? round($kb, 1) . ' KB'
            : round($kb / 1024, 1) . ' MB';
    }
}
