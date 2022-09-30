<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    public $table   = 'tbl_cms_menu';
    public $fillable = ['name', 'read', 'write'];

    protected static function boot()
    {
        parent::boot();
    }
}