<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SortDisplay extends Model
{

    protected static function boot()
    {
        parent::boot();
    }

    public $table = 'tbl_sort_display';

}
