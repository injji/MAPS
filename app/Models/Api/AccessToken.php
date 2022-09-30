<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
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
    // protected $table = 'tokens';
    protected $table = 'tbl_api_tokens';
    protected $primaryKey = 'idx';
    public $timestamps = false;
}
