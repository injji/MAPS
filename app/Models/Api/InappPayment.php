<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class InappPayment extends Model
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
    protected $table = 'tbl_inapp_payment';
}
