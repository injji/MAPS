<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, Builder};

class Payment extends Model
{
    public $table = 'tbl_payment';
    public $fillable = ['client_service_id', 'client_id', 'agent_id', 'service_id', 'site_id', 'plan_id', 'order_no', 'amount', 'refund_amount', 'currency', 'type', 'payment_type', 'service_start_at',
                'service_end_at', 'service_stop_at', 'refund_flag', 'refund_reason', 'refusal_reason', 'refund_status', 'refund_request_at',
                'refund_complete_at'];
    public $dates = ['service_start_at', 'service_end_at'];
    public $appends = ['type_text', 'payment_type_text', 'refund_status_text'];
    public static $paymentType = [
        'payment.card',
        'payment.account',
    ];
    public static $type = [
        'process.new',
        'process.extension',
        'process.refund',
    ];
    public static $refundStatus = [
        'refund.status1',
        'refund.status2',
        'refund.status3',
        'refund.status4',
        'refund.status5',
        'refund.status6',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * 기간 설정
     *
     * @param int $term
     * @return void
     */
    public function setTerm($plan)
    {
        $this->service_end_at = $this->service_start_at->{$plan->term_unit == 0 ? 'addMonths' : 'addDays'}($plan->term)->endOfDay();
        if ($plan->term_unit == 0) {
            $this->service_end_at = $this->service_end_at->subDay();
        }
        $this->plan = $plan->name;
    }

    /**
     * type_text attribute
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        return __(self::$type[$this->type] ?? '');
    }

    /**
     * payment_type_text attribute
     *
     * @return string
     */
    public function getPaymentTypeTextAttribute()
    {
        return __(self::$paymentType[$this->payment_type] ?? '');
    }

    /**
     * refund_stauts_text attribute
     *
     * @return string
     */
    public function getRefundStatusTextAttribute()
    {
        return __(self::$refundStatus[$this->refund_status] ?? '');
    }

    /**
     * 결제 방식
     *
     * @return array
     */
    public static function getPaymentTypes()
    {
        $result = [];
        foreach (self::$paymentType as $val => $text) {
            $result[] =[
                'value' => $val,
                'text' => __($text),
            ];
        }
        return $result;
    }

    /**
     * 결제 구분
     *
     * @return array
     */
    public static function getTypes()
    {
        $result = [];
        foreach (self::$type as $val => $text) {
            $result[] = [
                'value' => $val,
                'text' => __($text),
            ];
        }
        return $result;
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
     * service 관계성
     *
     *
     */
    public function service()
    {
        return $this->hasOne(\App\Models\Agent\Service::class, 'id', 'service_id');
    }

    /**
     * 플랜 정보 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function plan()
    {
        return $this->hasOne(\App\Models\Agent\ServicePlan::class, 'id', 'plan_id');
    }
}
