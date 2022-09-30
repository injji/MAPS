<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class SiteInfo extends Model
{

    public $table    = 'tbl_site_info';
    public $fillable = ['company_name', 'address', 'officer_name', 'phone', 'email', 'buss_num', 'personal_manager', 'fax', 'tax_mail'];

    protected static function boot()
    {
        parent::boot();
    }


}
