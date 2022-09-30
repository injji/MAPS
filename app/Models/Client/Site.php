<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    public $table = 'tbl_client_site';
    public $fillable = ['client_sid', 'client_id', 'name', 'url', 'type', 'hostname'];

    public static $typeText = [
        'form.site.type.option.shop',
        'form.site.type.option.homepage',
        'form.site.type.option.news',
        'form.site.type.option.etc',
    ];

    public static $hostText = [
        'form.site.hostname.option.etc',
        'form.site.hostname.option.cafe24',
        'form.site.hostname.option.make',
        'form.site.hostname.option.godo',
    ];

    /**
     * type 이름 return
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        return self::$typeText[$this->type] ? __(self::$typeText[$this->type]) : '';
    }

    /**
     * host 이름 return
     *
     * @return string
     */
    public function getHostTextAttribute()
    {
        return self::$hostText[$this->hostname] ? __(self::$hostText[$this->hostname]) : '';
    }

    /**
     * service 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->hasMany(\App\Models\Client\Service::class, 'site_id', 'id');
                    // ->whereIn('process', [1, 2]);
    }

    /**
     * service 관계성 사이트삭제시 서비스 중지 체크
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function serviceStopProcess()
    // {
    //     return $this->hasMany(\App\Models\Client\Service::class, 'site_id', 'id')
    //                 ->where('process','<>',4);
    // }
}
