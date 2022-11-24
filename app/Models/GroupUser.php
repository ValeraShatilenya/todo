<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GroupUser extends Model
{
    use HasFactory;

    protected $table  = 'group_user';

    public  $timestamps = false;

    protected $fillable = ['admin'];

    public function scopeUserId($query, ...$ids)
    {
        return $query->whereIn('user_id', $ids);
    }

    public function scopeAdmin($query)
    {
        return $query->where('admin', 1);
    }
}
