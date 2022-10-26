<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path', 'task_id', 'group_task_id'];

    const UPDATED_AT = null;

    public function scopeOnlyTask($query)
    {
        return $query->whereNotNull('task_id');
    }

    public function scopeOnlyGroupTask($query)
    {
        return $query->whereNotNull('group_task_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function groupTask(): BelongsTo
    {
        return $this->belongsTo(GroupTask::class);
    }
}
