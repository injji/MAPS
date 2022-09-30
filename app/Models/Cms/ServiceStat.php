<?php

namespace App\Models\Cms;

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
    public static function getMonthChartDataSql ($st_date, $ed_date) {

      $sql = '
            FROM
            (
            SELECT 
            "'.$st_date.'"+INTERVAL m MONTH as m1
                FROM
                (
                    SELECT @rownum:=@rownum+1 as m FROM
                    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t1,
                    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t2,
                    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t3,
                    (SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4) t4,
                    (SELECT @rownum:=-1) t0
                ) d1
            ) d2 
            WHERE m1<="'.$ed_date.'"
            ORDER BY m1';

      return $sql;
    }

    /**
     * 신청 chart data
     *
     */
    public static function getUsingDatas($st_date, $ed_date, $tab)
    {

        if($tab == 1) {     // 일별
            $sql = 'SELECT DATE_FORMAT(a.date, "%m.%d") AS day,
                    (SELECT SUM(amount) FROM tbl_payment WHERE created_at LIKE CONCAT(a.date,"","%") AND refund_flag=0) AS pay_sum,
                    (SELECT COUNT(*) FROM tbl_user_log   WHERE created_at LIKE CONCAT(a.date,"","%")) AS dau_cnt,
                    (SELECT COUNT(*) FROM tbl_users      WHERE created_at LIKE CONCAT(a.date,"","%") AND type=1) AS new_cnt1,
                    (SELECT COUNT(*) FROM tbl_users      WHERE created_at LIKE CONCAT(a.date,"","%") AND type=2) AS new_cnt2,
                    (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(a.date,"","%")) AS req_cnt';
            $sql .= self::getChartDataSql($st_date, $ed_date);
        }
        if($tab == 2) {     // 주별
            $sql = 'SELECT 
                    WEEK(created_at) AS week_num, 
                    DATE_ADD( DATE(created_at), INTERVAL (7 - DAYOFWEEK( created_at )) DAY) day, 
                    (SELECT SUM(amount) FROM tbl_payment WHERE WEEK(created_at)=WEEK(day) AND refund_flag=0) AS pay_sum,
                    (SELECT COUNT(*) FROM tbl_user_log   WHERE WEEK(created_at)=WEEK(day)) AS dau_cnt,
                    (SELECT COUNT(*) FROM tbl_users      WHERE WEEK(created_at)=WEEK(day) AND type=1) AS new_cnt1,
                    (SELECT COUNT(*) FROM tbl_users      WHERE WEEK(created_at)=WEEK(day) AND type=2) AS new_cnt2,
                    (SELECT COUNT(*) FROM tbl_client_service WHERE WEEK(created_at)=WEEK(day)) AS req_cnt
                    FROM tbl_user_log 
                    WHERE (created_at BETWEEN "'.$st_date.'" AND "'.$ed_date.'") 
                    GROUP BY day;';
        }
        if($tab == 3) {     // 월별
            $sql = 'SELECT DATE_FORMAT(m1, "%Y-%m") AS day,
                    (SELECT SUM(amount) FROM tbl_payment WHERE created_at LIKE CONCAT(DATE_FORMAT(m1, "%Y-%m"),"","%") AND refund_flag=0) AS pay_sum,
                    (SELECT COUNT(*) FROM tbl_user_log   WHERE created_at LIKE CONCAT(DATE_FORMAT(m1, "%Y-%m"),"","%")) AS dau_cnt,
                    (SELECT COUNT(*) FROM tbl_users      WHERE created_at LIKE CONCAT(DATE_FORMAT(m1, "%Y-%m"),"","%") AND type=1) AS new_cnt1,
                    (SELECT COUNT(*) FROM tbl_users      WHERE created_at LIKE CONCAT(DATE_FORMAT(m1, "%Y-%m"),"","%") AND type=2) AS new_cnt2,
                    (SELECT COUNT(*) FROM tbl_client_service WHERE created_at LIKE CONCAT(DATE_FORMAT(m1, "%Y-%m"),"","%")) AS req_cnt';

            $sql .= self::getMonthChartDataSql($st_date, $ed_date);
        }

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
