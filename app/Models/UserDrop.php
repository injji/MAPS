<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UserDrop extends Model
{

    protected $table = 'tbl_user_drop';

    protected $fillable = [
        'user_type', 'account', 'company_name', 'reason', 'status', 'admin_reason', 'dropped_at'
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * 회원탈퇴 고객사 서비스 수 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function client_service()
    {
        return $this->hasMany(\App\Models\Client\Service::class, 'client_id', 'client_id');
    }

    /**
     * 회원탈퇴 제휴사 서비스 수 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function agent_service()
    {
        return $this->hasMany(\App\Models\Agent\Service::class, 'agent_id', 'client_id');
    }
}
