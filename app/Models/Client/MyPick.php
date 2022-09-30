<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class MyPick extends Model
{
    public $table = 'tbl_client_my_pick';
    public $fillable = ['client_id', 'service_id'];
    
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * service 관계성
     *
     *
     */
    public function service()
    {
        return $this->hasOne(\App\Models\Agent\Service::class, 'id', 'service_id');
    }
}
