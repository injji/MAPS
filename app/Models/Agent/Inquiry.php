<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    public $table = 'tbl_agent_inquiry';
    public $fillable = ['lang', 'agent_id', 'type', 'title', 'content', 'question_file', 'answer', 'answer_file', 'answered_at'];    
    public $appends = ['type_text'];
    // public static $type = [
    //     'inquiry.type10',
    //     'inquiry.type11',
    //     'inquiry.type12',
    //     'inquiry.type13',
    //     'inquiry.type14'
    // ];

    protected static function boot()
    {
        parent::boot();
    }
   
    /**
     * type text attribute
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        // return __(self::$type[$this->type] ?? '');
        return explode(',', \App\Models\Cms\QuestionOption::where('type', 2)->first()->content)[$this->type];
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
}
