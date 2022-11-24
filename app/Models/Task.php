<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['title', 'status', 'description', 'user_id', 'completed'];

    public function scopeNotCompleted($query)
    {
        return $query->whereNull('completed');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed');
    }

    public function scopeUserId($query, ...$ids)
    {
        return $query->whereIn('user_id', $ids);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'filable');
    }
}
