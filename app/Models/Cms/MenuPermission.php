<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class MenuPermission extends Model
{

    public $table   = 'tbl_cms_menu_permission';
    public $fillable = ['user_id', 'menu_id', 'level'];

    protected static function boot()
    {
        parent::boot();
    }
}