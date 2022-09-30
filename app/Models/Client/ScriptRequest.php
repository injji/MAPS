<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class ScriptRequest extends Model
{
    public $table = 'tbl_script_request';
    public $fillable = ['name', 'hostname', 'admin_url', 'account', 'password', 'flag'];

    protected static function boot()
    {
        parent::boot();
    }

}
