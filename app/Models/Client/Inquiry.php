<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    public $table = 'tbl_client_inquiry';
    public $fillable = ['lang', 'client_id', 'service_id', 'type', 'title', 'content', 'question_file', 'answer', 'answer_file', 'answered_at'];    
    public $appends = ['type_text'];
    // public static $type = [
    //     'inquiry.type0',
    //     'inquiry.type1',
    //     'inquiry.type2',
    //     'inquiry.type3'
    // ];

    protected static function boot()
    {
        parent::boot();
    }
    
    /**
     * service 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne(\App\Models\Agent\Service::class, 'id', 'service_id');
    }

    /**
     * type text attribute
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        // return __(self::$type[$this->type] ?? '');
        return explode(',', \App\Models\Cms\QuestionOption::where('type', 1)->first()->content)[$this->type];
    }

    /**
     * writer 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(\App\Models\Users::class, 'id', 'client_id');
    }
}
