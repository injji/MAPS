<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{

    protected static function boot()
    {
        parent::boot();
    }

    public $table = 'tbl_store_banner';

    /**
     * banner
     *
     */
    public static function getBanners($type)
    {
        $sql = 'SELECT * FROM tbl_store_banner WHERE type="'.$type.'" ORDER BY `sort` ASC';

        return DB::select($sql);
    }

}
