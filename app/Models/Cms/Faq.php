<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{

    public $table   = 'tbl_faq';
    public $fillable = ['faq_category', 'content', 'category'];

    protected static function boot()
    {
        parent::boot();
    }


}
