<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    public $table = 'tbl_notice';
    public $fillable = ['type', 'title', 'content'];
    
    protected static function boot()
    {
        parent::boot();
    }
}
