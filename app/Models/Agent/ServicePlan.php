<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePlan extends Model
{
    use SoftDeletes;

    public $table = 'tbl_service_plan';
    public $fillable = ['service_id', 'name', 'term', 'term_unit', 'amount', 'currency', 'description'];
    const CREATED_AT = null;
    const UPDATED_AT = null;
    
    public static $termUnit = [
        'messages.month',
        'messages.day',
    ];

    /**
     * term unit text attribute
     *
     * @return string
     */
    public function getTermUnitTextAttribute()
    {
        return __(self::$termUnit[$this->term_unit], '');
    }

    /**
     * currency text attribute
     *
     * @return string
     */
    public function getCurrencyTextAttribute()
    {
        return config('app.currency')[$this->currency];
    }
}
