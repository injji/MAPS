<?php

namespace App\Models\Byapps;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'byapps';
    protected $table = 'BYAPPS_user_info';
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

    /**
     * MAPS 회원가입 여부
     *
     * @return void
     */
    public function isRegistered($id)
    {
        $maps = \App\Models\Users::where('byapps_id', $id)->exists();
        $maps_user = false;

        if($maps){
            $maps_user = true;
        }

        return $maps_user;
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

    /**
     * apps 관계성
     *
     *
     */
    public function apps()
    {
        return $this->hasMany(\App\Models\Byapps\Apps::class, 'mem_id', 'mem_id');
    }
}
