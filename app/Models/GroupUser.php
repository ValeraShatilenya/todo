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

    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }

    public function scopeAdmin($query)
    {
        return $query->where('admin', 1);
    }
}
