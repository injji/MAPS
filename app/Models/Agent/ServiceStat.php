<?php

namespace App\Models\Agent;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceStat extends Model
{
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();
    }

    public $table = 'tbl_service_plan';

    /**
     * generate label
     *
     */
    public static function getChartDataSql ($st_date, $ed_date) {

        $sql = '';
        $sql .= ' FROM (SELECT "'.$ed_date.'" - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS date';
        $sql .= ' FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4';
        $sql .= ' UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a';
        $sql .= ' CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4';
        $sql .= ' UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b';
        $sql .= ' CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3';
        $sql .= ' UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7';
        $sql .= ' UNION ALL SELECT 8 UNION ALL SELECT 9) AS c) a';
        $sql .= ' WHERE a.date BETWEEN "'.$st_date.'" AND "'.$ed_date.'" ORDER BY a.date';

        return $sql;
    }

    /**
     * 신청 chart data
     *
     */
    public static function getOrderDatas($st_date, $ed_date, $lang)
    {
        $sql = 'SELECT DATE_FORMAT(a.date, "%m.%d") AS day,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%") AND process=1 AND lang="'.$lang.'") AS wait_cnt,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%") AND process=2 AND lang="'.$lang.'") AS complete_cnt,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%") AND process=3 AND lang="'.$lang.'") AS expire_cnt';

        $sql .= self::getChartDataSql($st_date, $ed_date);

        return DB::select($sql);
    }

    /**
     * 매출 chart data
     *
     */
    public static function getSalesDatas($st_date, $ed_date, $lang)
    {
        $sql = 'SELECT DATE_FORMAT(a.date, "%m.%d") AS day,
                (SELECT COUNT(*)    FROM tbl_payment WHERE created_at LIKE CONCAT(a.date,"","%") AND type=0 AND lang="'.$lang.'") AS new_cnt,
                (SELECT SUM(amount) FROM tbl_payment WHERE created_at LIKE CONCAT(a.date,"","%") AND type=0 AND lang="'.$lang.'") AS new_sum,
                (SELECT COUNT(*)    FROM tbl_payment WHERE created_at LIKE CONCAT(a.date,"","%") AND type=1 AND lang="'.$lang.'") AS extend_cnt,
                (SELECT SUM(amount) FROM tbl_payment WHERE created_at LIKE CONCAT(a.date,"","%") AND type=1 AND lang="'.$lang.'") AS extend_sum';

        $sql .= self::getChartDataSql($st_date, $ed_date);

        return DB::select($sql);
    }

    /**
     * 서비스 chart data
     *
     */
    public static function getServiceDatas($st_date, $ed_date, $lang)
    {
        $sql = 'SELECT DATE_FORMAT(a.date, "%m.%d") AS day,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at = a.date - INTERVAL 1 DAY  AND process=2 AND lang="'.$lang.'") AS prev_using_cnt,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%") AND process=2 AND lang="'.$lang.'") AS using_cnt,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%") AND process=3 AND lang="'.$lang.'") AS expire_cnt,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at = a.date - INTERVAL 1 DAY  AND process=1 AND lang="'.$lang.'") AS prev_wait_cnt,
                (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%") AND process=1 AND lang="'.$lang.'") AS wait_cnt';

        $sql .= self::getChartDataSql($st_date, $ed_date);

        return DB::select($sql);
    }

}
