<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Route;
use DB;

class Users extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_users';

    protected $fillable = [
        'account', 'password', 'type', 'company_name', 'manager_name', 'manager_phone', 'manager_email', 'director_name', 'director_phone',
        'director_email', 'business_no', 'business_registration', 'address', 'homepage_url', 'order_report_number', 'bank_name',
        'account_holder', 'account_number', 'tax_email', 'fees', 'specialized_field', 'tax_company_name', 'tax_business_no',
        'tax_director_name', 'tax_address', 'tax_business_registration', 'inquiry_time', 'lang', 'sel_site_id', 'byapps_id','self_payment' , 'dropped_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * 사이트 관계성 (고객사만)
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function site()
    {
        return $this->hasMany(\App\Models\Client\Site::class, 'client_id', 'id');
    }

    /**
     * 현재 활성화된 site (고객사만)
     *
     * @return App\Models\Client\Site
     */
    public function getCurrentSiteAttribute()
    {
        return $this->site->find($this->sel_site_id);
    }

    /**
     * agent service 관계성 지장
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->hasMany(\App\Models\Agent\Service::class, 'agent_id', 'id');
    }

    /**
     * 고객사 주문 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function client_service()
    {
        return $this->hasMany(\App\Models\Client\Service::class, 'client_id', 'id')
            ->where('lang', \Lang::getLocale())
            ->orderBy('created_at', 'desc');
    }

    /**
     * 리뷰 관계성 지장
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function review()
    {
        return $this->hasMany(\App\Models\Client\Review::class, 'agent_id', 'id');
    }

    /**
     * payment 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment()
    {
        return $this->hasMany(\App\Models\Payment::class, 'agent_id', 'id');
    }

    /**
     * payment 신규 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment0()
    {
        return $this->hasMany(\App\Models\Payment::class, 'agent_id', 'id')->where('type', 0);
    }

    /**
     * payment 연장 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment1()
    {
        return $this->hasMany(\App\Models\Payment::class, 'agent_id', 'id')->where('type', 1);
    }

    // /**
    //  * drop
    //  *
    //  * @return Illuminate\Database\Eloquent\Relations\HasOne
    //  */
    // public function drop()
    // {
    //     return $this->hasOne(\App\Models\UserDrop::class, 'client_id', 'id');
    // }
}
