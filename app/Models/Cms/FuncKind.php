<?php

namespace App\Models\Cms;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuncKind extends Model
{

    protected static function boot()
    {
        parent::boot();
    }

    public $table = 'tbl_store_func_kind';

    /**
     * 기능별분류
     *
     */
    public static function getFuncKind($keyword)
    {
        $where = '1';
        if($keyword) {
            $where .= ' AND title LIKE "%'.$keyword.'%"';
        }
        $sql = 'SELECT *, 
                    (SELECT GROUP_CONCAT(ko) FROM tbl_service_category  WHERE FIND_IN_SET(id, kind)) AS kinds,
                    (SELECT COUNT(*)         FROM tbl_agent_service     WHERE FIND_IN_SET(id, service)) AS service_cnt
                    FROM tbl_store_func_kind WHERE '.$where.' ORDER BY `weight` DESC';

        return DB::select($sql);
    }

    public static function getFuncKindById($id)
    {
        $sql = 'SELECT *, 
                    (SELECT GROUP_CONCAT(ko) FROM tbl_service_category  WHERE FIND_IN_SET(id, kind)) AS kinds
                    FROM tbl_store_func_kind WHERE id="'.$id.'"';

        return DB::select($sql);
    }

}
