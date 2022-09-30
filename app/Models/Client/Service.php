<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $table = 'tbl_client_service';
    public $fillable = ['client_id', 'service_id', 'site_id', 'process', 'status', 'set_req_url', 'period_type',
            'period', 'service_start_at', 'service_end_at', 'review_flag'];
    public $dates = ['service_start_at', 'service_end_at'];
    public $appends = ['process_text'];
    public static $process = [
        'process.wait_request',
        'process.apply',
        'process.using',
        'process.expired',
        'process.stop',
        'process.dell'
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * process list
     *
     * @return array
     */
    public static function getProcesses()
    {
        $result = [];
        foreach (self::$process as $val => $text) {
            $result[] = [
                'value' => $val,
                'text' => __($text),
            ];
        }
        return $result;
    }

    /**
     * process text attribute
     *
     * @return string
     */
    public function getProcessTextAttribute()
    {
        return __(self::$process[$this->process] ?? '');
    }

    public function serviceCnt($service_id)
    {
        return $this->hasMany($this, 'service_id', $service_id)->count();
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

    /**
     * site 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function site()
    {
        return $this->hasOne(\App\Models\Client\Site::class, 'id', 'site_id');
    }

    /**
     * payment 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function payment()
    {
        return $this->hasMany(\App\Models\Payment::class, 'client_service_id', 'id');
    }
}
