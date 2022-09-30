<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;

class AdminUser extends User
{
    public $table = 'tbl_cms_users';    
    protected $fillable = ['oauth_id', 'oauth_type', 'name', 'level', 'use', 'super'];
    
    public static $oauthType = [
        'innofam'
    ];

    /**
     * oauth type 숫자로 변경
     *
     * @param string $oauthType
     * @return int
     */
    public static function getOuathType($oauthType)
    {
        if (!in_array($oauthType, self::$oauthType)) {
            throw new \Exception("Unknown oauth type", 1);
        }
        return array_search($oauthType, self::$oauthType);
    }

    /**
     * 이노팸 유저 스코프
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $idx
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInnofam($query, $idx)
    {
        return $query->where('oauth_id', $idx)->where('oauth_type', self::getOuathType('innofam'));
    }

    /**
     * menu permission 관계성 지장
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permission()
    {
        return $this->hasMany(\App\Models\Cms\MenuPermission::class, 'user_id', 'id')->orderBy('menu_id', 'asc');
    }

    public function getWritePermission($menu_id)
    {        
        $write_level = \App\Models\Cms\Menu::find($menu_id)->write;
        $user_level = $this->level;
        $menu_permission = $this->permission()->where('menu_id', $menu_id)->first();
        
        if ($menu_permission)
            $user_level = $menu_permission->level;

        if ($write_level <= $user_level)
            return true;
        else
            return false;
    }
}
