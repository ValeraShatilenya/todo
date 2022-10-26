<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class GroupTask extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['group_id', 'user_id', 'title', 'description', 'completed', 'completed_user_id'];

    public function scopeNotCompleted($query)
    {
        return $query->whereNull('completed');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed');
    }

    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_user_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}
