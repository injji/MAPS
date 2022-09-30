<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{

    public $table   = 'tbl_notice';
    public $appends = ['type_text'];

    public static $type = [
        '전체',
        '고객사',
        '제휴사'
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
