<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    public $table = 'tbl_user_log';
    public $fillable = ['user_id', 'created_at'];
    
    protected static function boot()
    {
        parent::boot();
    }
}
