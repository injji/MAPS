<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{

    public $table   = 'tbl_store_content';
    public $fillable = ['id', 'title', 'description', 'content', 'img', 'banner', 'order', 'hits'];

    protected static function boot()
    {
        parent::boot();
    }


}
