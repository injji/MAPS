<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\{Model, Builder};
use Str;

class Service extends Model
{
    public $table = 'tbl_agent_service';
    public $fillable = ['agent_id', 'process', 'lang', 'default_lang', 'name', 'url', 'category1', 'category2', 'visible', 'icon',
        'redirect_url', 'api_id', 'api_key', 'api_key_note', 'api_scope', 'version', 'script_url', 'release_note', 'banner_image',
        'youtube_url', 'service_info', 'full_description', 'ad_url', 'sample_url', 'in_app_payment', 'contact_type', 'contact_phone',
        'contact_email', 'search_keyword', 'specification', 'currency', 'amount_min', 'amount_max', 'free_term', 'reject_reason',
        'request_at', 'complete_at', 'view_cnt', 'request_cnt'];
    public $appends = ['process_text'];

    public static $process = [
        'process.wait_registration', // 등록대기
        'process.review', // 신규 심사중
        'process.reject', // 심사거절
        'process.sale',   // 판매중
        'process.review', // 업데이트 심사중
        'process.sale_stop', // 판매중지
    ];

    /**
     * bootstrap
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * 결제 정보 설정
     *
     * @param array
     * @return void
     *
     * @throws Exception pk 없으면
     */
    public function setPaymentPlan($payments)
    {
        if ($this->getKey() == null) {
            throw new \Exception("Error Processing Request", 1);
        }

        if ($payments == null) {
            $this->plan()
                ->delete();
            return;
        }

        $this->plan()
            ->whereNotIn(
                'name',
                collect($payments)->pluck('name')->toArray()
            )->delete();

        foreach ($payments as $id => $payment) {
            if (preg_match('/^new-/', $id)) {
                $new = new ServicePlan([
                    'service_id' => $this->id,
                ]);
                $new->fill($payment);
                $new->save();
            } else {
                ServicePlan::find($id)->update($payment);
            }
        }
    }

    /**
     * 플랜 정보 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function plan()
    {
        return $this->hasMany(ServicePlan::class, 'service_id');
    }

    /**
     * FAQ 저장
     *
     * @param array $faqs
     * @return void
     *
     * @throws Exception pk 없으면
     */
    public function setFaq($faqs)
    {
        if ($this->getKey() == null) {
            throw new \Exception("Error Processing Request", 1);
        }

        if ($faqs == null) {
            $this->faq()
                ->delete();
            return;
        }

        $this->faq()
            ->whereNotIn(
                'question',
                collect($faqs)->pluck('question')->toArray()
            )->delete();

        foreach ($faqs as $id => $faq) {
            if (preg_match('/^new-/', $id)) {
                $new = new ServiceFaq([
                    'service_id' => $this->id,
                ]);
                $new->fill($faq);
                $new->save();
            } else {
                ServiceFaq::find($id)->update($faq);
            }
        }
    }

    /**
     * faq 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function faq()
    {
        return $this->hasMany(ServiceFaq::class, 'service_id');
    }

    /**
     * user 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(\App\Models\Users::class, 'id', 'agent_id');
    }

    /**
     * 사용자 리뷰
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function review()
    {
        return $this->hasMany(\App\Models\Client\Review::class, 'service_id');
    }

    public function inquiry()
    {
        return $this->hasMany(\App\Models\Client\Inquiry::class, 'service_id');
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
            $result[$val] = __($text);
        }
        return $result;
    }

    /**
     * process_text attribute
     *
     * @return string
     */
    public function getProcessTextAttribute()
    {
        return __(self::$process[$this->process] ?? '');
    }

    /**
     * 카테고리1 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cat1()
    {
        return $this->hasOne(\App\Models\Agent\ServiceCategory::class, 'id', 'category1');
    }

    /**
     * 카테고리2 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cat2()
    {
        return $this->hasOne(\App\Models\Agent\ServiceCategory::class, 'id', 'category2');
    }

    /**
     * service 이용 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function service()
    {
        return $this->hasMany(\App\Models\Client\Service::class, 'service_id', 'id');
    }

    /**
     * payment 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment()
    {
        return $this->hasMany(\App\Models\Payment::class, 'service_id', 'id');
    }

    /**
     * payment 신규 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment0()
    {
        return $this->hasMany(\App\Models\Payment::class, 'service_id', 'id')->where('type', 0);
    }

    /**
     * payment 연장 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payment1()
    {
        return $this->hasMany(\App\Models\Payment::class, 'service_id', 'id')->where('type', 1);
    }
}
