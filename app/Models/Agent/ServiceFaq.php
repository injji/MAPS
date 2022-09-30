<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFaq extends Model
{
    use SoftDeletes;

    public $table = 'tbl_service_faq';
    public $fillable = ['service_id', 'faq_category', 'category', 'question', 'answer', 'file'];
    const CREATED_AT = null;
    const UPDATED_AT = null;

    public static $category = [
        'messages.qna.service',
        'messages.qna.payment',
        'messages.qna.etc',
    ];

    /**
     * 전체 카테고리
     *
     * @return array
     */
    public static function getCategorys()
    {
        $result = [];
        foreach (self::$category as $val => $lang) {
            $result[] = [
                'value' => $val,
                'text' => __($lang),
            ];
        }
        return $result;
    }

    /**
     * category text attrigute
     *
     * @return string
     */
    public function getCategoryTextAttribute()
    {
        return __(self::$category[$this->category] ?? '');
    }
}
