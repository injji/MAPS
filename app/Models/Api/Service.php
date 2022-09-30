<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    // protected $fillable = [
    //     'client_id',
    //     'redirect_url',
    //     'service_key',
    //     'redirect_url',
    // ];

    // protected $guarded = [];
    // protected $table = 'service_test';
    protected $table = 'tbl_agent_service';
    // protected $primaryKey = 'script_id';
    public $timestamps = false;
}
