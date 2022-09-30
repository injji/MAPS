<?php

namespace App\Models\Agent;

use Illuminate\Database\Eloquent\{Model};
use Illuminate\Support\Facades\App;

class ServiceCategory extends Model
{
    public $table = 'tbl_service_category';
    public $fillable = ['ko', 'en', 'depth', 'parent', 'expo', 'sort'];
    public $appends = ['text'];

    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();
    }

    /**
     * 전체 카테고리
     *
     * @return Illuminate\Database\Eloquent\Collection|App\Models/Agent/ServiceCategory
     */
    public static function getCate()
    {
        return (new self)->with('child')
            ->depth()
            ->orderBy('sort', 'asc')
            ->get();
    }

    /**
     * select optin 용 array
     *
     * @return array
     */
    public static function getCateForSelectBox()
    {
        $result = [];
        foreach (self::getCate() as $row) {
            $sub = [];
            foreach ($row->child as $child) {
                $sub[] = [
                    'value' => $child->id,
                    'text' => $child[\Lang::getLocale()],
                ];
            }
            $result[] = [
                'value' => $row->id,
                'text' => $row[\Lang::getLocale()],
                'child' => $sub,
            ];
        }
        return $result;
    }

    /**
     * depth scope
     *
     * @return void
     */
    public function scopeDepth($query, $depth = 1)
    {
        $query->where('depth', $depth);
    }

    /**
     * 하위 카테고리 관계성 지정
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function child()
    {
        return $this->hasMany(self::class, 'parent', 'id')->orderBy('sort', 'asc');
    }

    /**
     * text attribute
     *
     * @return string
     */
    public function getTextAttribute()
    {
        return $this[\Lang::getLocale()] ?? '';
    }
}
