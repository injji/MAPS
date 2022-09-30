<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public $table = 'tbl_client_review';
    public $fillable = ['lang', 'client_id', 'service_id', 'rating', 'content', 'answer', 'answered_at', 'visible'];
    public $casts = [
        'answered_at' => 'datetime',
    ];
    
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
     * writer 관계성
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
        return $this->hasOne(\App\Models\Users::class, 'id', 'client_id');
    }
}
