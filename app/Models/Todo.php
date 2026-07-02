<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Todo extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('todo')
            ->logOnly([
                'title',
                'description',
                'priority',
                'deadline',
                'status',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
    public function subtasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subtask::class)->oldest();
    }

    public function attachments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TodoAttachment::class)->latest();
    }

}
