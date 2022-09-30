<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class SiteCard extends Model
{

    public $table    = 'tbl_site_card';
    public $fillable = ['active', 'bank', 'account', 'owner'];

    protected static function boot()
    {
        parent::boot();
    }


}
