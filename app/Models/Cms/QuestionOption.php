<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{

    public $table   = 'tbl_question_option';
    public $fillable = ['content', 'type'];
    public $appends = ['type_text'];

    public static $type = [
        '심사거절',
        '고객사문의',
        '제휴사문의',
        '탈퇴사유'
    ];

    protected static function boot()
    {
        parent::boot();
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

}
