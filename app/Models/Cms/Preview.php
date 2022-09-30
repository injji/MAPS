<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Preview extends Model
{

    public $table   = 'tbl_preview';
    // public $fillable = ['faq_category', 'content', 'category'];

    protected static function boot()
    {
        parent::boot();
    }


}
