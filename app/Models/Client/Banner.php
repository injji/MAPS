<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $table = 'tbl_store_banner';
    public $fillable = ['type', 'ko', 'en', 'jp', 'cn', 'tw', 'vn', 'status', 'url'];
    
    protected static function boot()
    {
        parent::boot();
    }
}
