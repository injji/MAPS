<?php

namespace App\Models\Byapps;

use Illuminate\Database\Eloquent\Model;

class Apps extends Model
{
    protected $connection = 'byapps';
    protected $table = 'BYAPPS_apps_data';
    protected $primaryKey = 'idx';
    const CREATED_AT = 'reg_date';

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            return $this->findOrFail($value);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    // /**
    //  * maps 유저 관계성 지정
    //  *
    //  * @return Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function mapsUser()
    // {
    //     return $this->hasOneThrough(
    //         \App\Models\Auth\FrontUser::class,
    //         \App\Models\Auth\FrontUserLink::class,
    //         'service_user_id',
    //         'id',
    //         'idx',
    //         'maps_user_id'
    //     );
    // }
}
