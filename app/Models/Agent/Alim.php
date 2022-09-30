<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;

class Alim extends Model
{
    public $table = 'tbl_alim';
    public $fillable = ['agent_id', 'content', 'type', 'move_id'];
    
    protected static function boot()
    {
        parent::boot();
    }
}
