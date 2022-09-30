<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Terms extends Model
{

    public $table   = 'tbl_terms';
    public $fillable = ['content', 'type'];

    protected static function boot()
    {
        parent::boot();
    }


}
