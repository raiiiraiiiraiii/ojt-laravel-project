<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodoAttachment extends Model
{
    protected $fillable = [
        'todo_id',
        'original_name',
        'path',
        'mime_type',
        'size',
    ];

    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }
}
