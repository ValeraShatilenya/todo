<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path'];

    protected $hidden = ['filable_id', 'filable_type', 'created_at'];

    const UPDATED_AT = null;

    public function filable()
    {
        return $this->morphTo();
    }

    public function scopeOnlyTask($query)
    {
        return $query->whereNotNull('task_id');
    }

    public function scopeOnlyGroupTask($query)
    {
        return $query->whereNotNull('group_task_id');
    }
}
