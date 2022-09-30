<?php
namespace App\Models\Store;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
//CCC 20220606
class Store extends Model
{
    public $table = 'tbl_store_banner';
    // public $fillable = ['id', 'type', 'ko', 'en', 'jp', 'cn', 'tw', 'vn', 'status', 'url', 'st_date', 'end_date'];


    protected static function boot()
    {
        parent::boot();
    }

    /**
     * get banner
     *
     */
    public static function getBanner($type)
    {
        $sql = 'SELECT * FROM tbl_store_banner WHERE status=1 AND type='.$type;
        $sql .= ' AND ((st_date<>"" AND st_date<=CURDATE()) OR st_date IS NULL) ';
        $sql .= ' AND ((end_date<>"" AND end_date>=CURDATE()) OR end_date IS NULL)';
        $sql .= ' ORDER BY sort ASC';
        return DB::select($sql);

        // $r_query = \App\Models\Client\Banner::where('status', 1);
        // $r_query->where('type', $type);
        // $r_query->where(function ($query_1) {
        //     $query_1->whereNull('st_date')->orWhere(function ($query_2) {
        //         $query_2->whereRaw('st_date <> "" ');
        //         $query_2->whereRaw('st_date <= NOW()');
        //     });
        // });
        // $r_query->where(function ($query_1) {
        //     $query_1->whereNull('end_date')->orWhere(function ($query_2) {
        //         $query_2->whereRaw('end_date <> "" ');
        //         $query_2->whereRaw('end_date >= NOW()');
        //     });
        // });
        // $r_query->orderBy('sort', 'ASC');
        // return $r_query->get();
    }

    /**
     * 기능별 분류
     *
     */
    public static function getFuncKind($limit = 0)
    {
        $query = DB::table('tbl_store_func_kind');
        $query->where('expo', 1);
        $query->whereRaw('((st_date<>"" AND st_date<=CURDATE()) OR st_date IS NULL)');
        $query->whereRaw('((end_date<>"" AND end_date>=CURDATE()) OR end_date IS NULL)');
        $query->orderBy('weight', 'desc');
        if($limit > 0)
        {
            $query->limit($limit);
        }
        return $query->get();
    }

    /**
     * 기능정보 얻기
     *
     */
    public static function getFuncInfo($id)
    {
        $query = DB::table('tbl_store_func_kind');
        $query->where('expo', 1);
        $query->where('id', $id);
        return $query->first();
    }

    /**
     * BEST
     *
     */
    public static function getBestService($prev_monday, $prev_sunday)
    {
        $limit = 4;

        $best = [];
        $sort_best = [];
        $sort_query = \App\Models\Cms\SortDisplay::where('tab', 'best')->first();
        if($sort_query){
            $sort_best = explode(',',$sort_query->service_id);
            $sort_cnt = count($sort_best );
            $limit = $limit - $sort_cnt;

            foreach($sort_best as $service_id){
                array_push($best, \App\Models\Agent\Service::find($service_id));
            }

        }

        // dd($limit);exit;

        $r_query = \App\Models\Agent\Service::where('visible', 1)
        ->whereNotIn('process', [0, 1, 2, 5])
        ->whereNotIn('id', $sort_best);
        $r_query->whereHas('service', function ($query) use ($prev_monday, $prev_sunday) {
            $query->whereBetween('created_at', [$prev_monday, $prev_sunday.' 23:59:59']);
        });
        $r_query->when('service', function($query) {
            $query->withCount('service')->orderBy('service_count', 'desc');
        });

        $best_display = $r_query->limit($limit)->get();

        foreach($best_display as $item){
            array_push($best, $item);
        }

        // dd($best); exit;
        return $best;
        // return $r_query->limit(4)->get();
    }

    /**
     * 만족율 200%
     *
     */
    public static function getSatisfy200($prev_monday, $prev_sunday, $limit)
    {

        $satisfy200 = [];
        $sort_satisfy200 = [];
        $sort_query = \App\Models\Cms\SortDisplay::where('tab', 'satisfy200')->first();
        if($sort_query){
            $sort_satisfy200 = explode(',',$sort_query->service_id);
            $sort_cnt = count($sort_satisfy200);
            $limit = $limit - $sort_cnt;

            foreach($sort_satisfy200 as $service_id){
                array_push($satisfy200, \App\Models\Agent\Service::find($service_id));
            }

        }

        $sub_query = \App\Models\Client\Review::where([['created_at', '>=', Carbon::now()->subDays(30)], ['created_at', '<=', Carbon::now()]]);
        $sub_query->select('service_id as clviewid', DB::raw('COUNT(id) as service_cnt'))->groupBy('service_id');

        $r_query = \App\Models\Agent\Service::where('visible', 1)
        ->whereNotIn('process', [0, 1, 2, 5])
        ->whereNotIn('id', $sort_satisfy200);
        $r_query->leftJoinSub($sub_query, 'subtbl', function ($join) {
            $join->on('id', '=', 'subtbl.clviewid');
        });
        $r_query->orderBy('service_cnt', 'desc');
        $r_query->when('review', function($query) {
            $query->withCount('review')->orderBy('review_count', 'desc');
        });
        if($limit > 0)
        {
            $r_query->limit($limit);
        }

        $satisfy_display = $r_query->limit($limit)->get();

        foreach($satisfy_display as $item){
            array_push($satisfy200, $item);
        }

        return $satisfy200;
    }

    /**
     * 필수 마테크
     *
     */
    public static function getMatech($limit)
    {

        $martech = [];
        $sort_martech = [];
        $sort_query = \App\Models\Cms\SortDisplay::where('tab', 'martech')->first();
        if($sort_query){
            $sort_martech = explode(',',$sort_query->service_id);
            $sort_cnt = count($sort_martech);
            $limit = $limit - $sort_cnt;

            foreach($sort_martech as $service_id){
                array_push($martech, \App\Models\Agent\Service::find($service_id));
            }
        }

        $r_query = \App\Models\Agent\Service::where('visible', 1)
            ->whereNotIn('process', [0, 1, 2, 5])
            ->whereNotIn('id', $sort_martech)
            ->when('service', function($query) {
                $query->withCount('service')->orderBy('service_count', 'desc');
            });
        if($limit > 0)
        {
            $r_query->limit($limit);
        }

        $martech_display = $r_query->limit($limit)->get();

        foreach($martech_display as $item){
            array_push($martech, $item);
        }

        return $martech;
    }

    /**
     *  lang : 서비스 검색  언어 default_lang(기본언어), lang(설정언어)가 같고
     *  search_type : txt
     *      keyword : name(서비스명 ), search_keyword(검색키워드), short_description(서비스 특징), full_description(서비스 상세 설명), service_info(대표 설명)
     *  search_type : amount
     *      min_price :최소, max_price :최대
     *  start : 시작 페이지
     *  limit : 페이지 노출 개수
     *  catagory : 카테고리 분류 category1(서비스 상위 카테고리)
     *  sort : 최신순, 판매량순, 낮은가격순, 별점순
     *  freecost : 무료, 유료, 인앱구매
     *  filtercatory : category2(서비스 하위 카테고리)
     */
    public static function getSearch($lang, $search_type, $keyword, $min_price, $max_price, $limit = 10, $sort = 1, $category = 0, $freecost=0, $filtercatory="")
    {
        $ratetbl = DB::table('tbl_agent_service as SVC')
                    ->leftJoin('tbl_client_review AS RVW', 'SVC.id', '=', 'RVW.service_id')
                    ->select('SVC.id as rvw_id', DB::raw('AVG(rating) as rating'))
                   ->groupBy('service_id');

        $r_query = \App\Models\Agent\Service::where('visible', 1)->whereNotIn('process', [0, 1, 2, 5]);
        $r_query->where(function ($query) use ($lang) {
            $query->where('default_lang', $lang)->orWhere('lang', $lang);
        });

        if($search_type == 'amount'){

            if($min_price == 0)
            {
                $r_query->whereRaw('(free_term > 0 OR ('.$min_price.' <= amount_min AND amount_min <= '.$max_price.') OR ('.$min_price.' <= amount_max AND amount_max<='.$max_price.'))');
            }else{
                // $r_query->where('amount_max', '>', 0);
                $r_query->whereRaw('(('.$min_price.' <= amount_min AND amount_min <= '.$max_price.') OR ('.$min_price.' <= amount_max AND amount_max<='.$max_price.'))');
            }
        }else{
            if($keyword != "" && $keyword != null)
            {
                $r_query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%".$keyword."%")
                          ->orWhere('search_keyword', 'like', "%".$keyword."%");
                });
            }
        }

        if($category > 0)
        {
            $r_query->where('category1', $category);
        }

        if($freecost > 0)
        {
            $r_query->where(function ($query) use ($freecost) {
                if($freecost == 111)
                {
                    $query->where('in_app_payment', '=', 1)//인앱 결제   100
                    ->orWhere('free_term', '>', 0)//무료          10
                    ->orWhere('amount_min', '>', 0);//유료        1
                }else{
                    if($freecost > 99)
                    {
                        $freecost = $freecost - 100;
                        $query->where('in_app_payment', 1);//인앱 결제   100
                    }else if($freecost > 9){
                        $freecost = $freecost - 10;
                        $query->where('free_term', '>', 0);//무료          10
                    }else{
                        $freecost = $freecost - 1;
                        $query->where('amount_min', '>', 0);//유료        1
                    }

                    if($freecost > 9){
                        $query->orWhere('free_term', '>', 0);//무료          10
                    }else if($freecost > 0){
                        $freecost = $freecost - 1;
                        $query->orWhere('amount_min', '>', 0);//유료        1
                    }
                }
            });
        }

        if($filtercatory)
        {

            $filter_array = explode(",", $filtercatory);
            $filter_array =  array_values(array_filter(array_map('trim', $filter_array), 'strlen'));
            if(count($filter_array) > 0)
            {
                $r_query->whereIn('category2', $filter_array);
            }
        }
        if($sort == 1)
        {//최신순
            $r_query->orderBy('created_at', 'desc');
        }else if($sort == 2)
        {//판매량순
            $r_query->when('service', function($query) {
                $query->withCount('service')->orderBy('service_count', 'desc');
            });
        }else if($sort == 3)
        {//낮은가격순
            $r_query->orderBy('amount_min', 'asc');
        }else if($sort == 4)
        {//별점순
            $r_query->leftJoinSub($ratetbl, 'ratetbl', function ($join) {
                    $join->on('id', '=', 'ratetbl.rvw_id');
                });
            $r_query->orderBy('rating', 'desc');
        }

        if($limit)
        {
            $r_query->limit($limit);
        }
        return $r_query->get();
    }

    public static function getSearchCategoryAll($lang, $search_type, $keyword, $min_price, $max_price, $sort = 1, $freecost=0, $filtercatory="")
    {
        $ratetbl = DB::table('tbl_agent_service as SVC')
                    ->leftJoin('tbl_client_review AS RVW', 'SVC.id', '=', 'RVW.service_id')
                    ->select('SVC.id as rvw_id', DB::raw('AVG(rating) as rating'))
                   ->groupBy('service_id');

        $r_query = \App\Models\Agent\Service::where('visible', 1)->whereNotIn('process', [0, 1, 2, 5]);
        $r_query->where(function ($query) use ($lang) {
            $query->where('default_lang', $lang)->orWhere('lang', $lang);
        });

        if($search_type == 'amount'){

            if($min_price == 0)
            {
                $r_query->whereRaw('(free_term > 0 OR ('.$min_price.' <= amount_min AND amount_min <= '.$max_price.') OR ('.$min_price.' <= amount_max AND amount_max<='.$max_price.'))');
            }else{
                // $r_query->where('amount_max', '>', 0);
                $r_query->whereRaw('(('.$min_price.' <= amount_min AND amount_min <= '.$max_price.') OR ('.$min_price.' <= amount_max AND amount_max<='.$max_price.'))');
            }
        }else{
            if($keyword != "" && $keyword != null)
            {
                $r_query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%".$keyword."%")
                          ->orWhere('search_keyword', 'like', "%".$keyword."%");
                });
            }
        }

        if($freecost > 0)
        {
            $r_query->where(function ($query) use ($freecost) {
                if($freecost == 111)
                {
                    $query->where('in_app_payment', '=', 1)//인앱 결제   100
                    ->orWhere('free_term', '>', 0)//무료          10
                    ->orWhere('amount_min', '>', 0);//유료        1
                }else{
                    if($freecost > 99)
                    {
                        $freecost = $freecost - 100;
                        $query->where('in_app_payment', 1);//인앱 결제   100
                    }else if($freecost > 9){
                        $freecost = $freecost - 10;
                        $query->where('free_term', '>', 0);//무료          10
                    }else{
                        $freecost = $freecost - 1;
                        $query->where('amount_min', '>', 0);//유료        1
                    }

                    if($freecost > 9){
                        $query->orWhere('free_term', '>', 0);//무료          10
                    }else if($freecost > 0){
                        $freecost = $freecost - 1;
                        $query->orWhere('amount_min', '>', 0);//유료        1
                    }
                }
            });
        }

        if($filtercatory)
        {

            $filter_array = explode(",", $filtercatory);
            $filter_array =  array_values(array_filter(array_map('trim', $filter_array), 'strlen'));
            if(count($filter_array) > 0)
            {
                $r_query->whereIn('category2', $filter_array);
            }
        }
        if($sort == 1)
        {//최신순
            $r_query->orderBy('created_at', 'desc');
        }else if($sort == 2)
        {//판매량순
            $r_query->when('service', function($query) {
                $query->withCount('service')->orderBy('service_count', 'desc');
            });
        }else if($sort == 3)
        {//낮은가격순
            $r_query->orderBy('amount_min', 'asc');
        }else if($sort == 4)
        {//별점순
            $r_query->leftJoinSub($ratetbl, 'ratetbl', function ($join) {
                    $join->on('id', '=', 'ratetbl.rvw_id');
                });
            $r_query->orderBy('rating', 'desc');
        }

        // if($limit)
        // {
        //     $r_query->limit($limit);
        // }
        return $r_query->get();
    }

    /**
     * 최근 한 달간 등록된 서비스의 카테고리 목록을 얻는다.
     */
    public static function getNewSvcCatagory()
    {

        $new = [];
        $sort_new = [];
        $sort_query = \App\Models\Cms\SortDisplay::where('tab', 'new')->first();
        $sort_category = [];
        if($sort_query){
            $sort_new = explode(',',$sort_query->service_id);

            $sort_display = \App\Models\Agent\Service::whereIn('id', $sort_new)->get();
            foreach($sort_display as $item){
                $new_category = DB::table('tbl_service_category')->selectRaw('id , '.\Lang::getLocale().' AS text')
                ->where('id', $item->category1)->first();
                array_push($new, $new_category);
                array_push($sort_category, $new_category->id);
            }
        }

        // dd($sort_category);
        $r_query = DB::table('tbl_agent_service AS SVC')
                        ->selectRaw('CT.id as id, CT.'.\Lang::getLocale().' AS text')
                        ->leftJoin('tbl_service_category AS CT', 'CT.id', '=', 'SVC.category1')
                        ->where('SVC.visible', '=', 1)
                        ->whereNotIn('process', [0, 1, 2, 5])
                        ->whereNotIn('SVC.id', $sort_new);
        if(\Lang::getLocale() != "ko")
        {
            $r_query->where('CT.expo', '=', 1);
        }
        $r_query->whereDate('created_at', '>=', Carbon::now()->subDays(30))
                        ->groupBy('SVC.category1')
                        ->orderBy('CT.sort');

        $category = $r_query->get();
        foreach($category as $item){
            if(!in_array($item->id, $sort_category)){
                array_push($new, $item);
            }
        }
        // $new = array_unique($new);
        // dd($new);

        return $new;
    }

    /**
     * 최근 한 달간 등록된 서비스의 카테고리로 서비스 목록을 얻는다.
     */
    public static function getNewServices($catgory)
    {

        $new = [];
        $sort_new = [];
        $sort_id = [];
        $sort_query = \App\Models\Cms\SortDisplay::where('tab', 'new')->first();
        if($sort_query){
            $sort_new = explode(',',$sort_query->service_id);

            $sort_display = \App\Models\Agent\Service::whereIn('id', $sort_new)->where('category1', $catgory)->get();
            foreach($sort_display as $item){
                array_push($new, $item);
                array_push($sort_id, $item->id);
            }
        }

        $r_query = \App\Models\Agent\Service::where('visible', 1)
        ->whereNotIn('process', [0, 1, 2, 5])
        ->whereNotIn('id', $sort_id)
        ->where('category1', $catgory)
        ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
        ->when(\Lang::getLocale() != "ko", function($query) {
            $query->whereHas('cat2', function($query) {
                $query->where('expo', 1);
            });
        })
        ->orderBy('created_at', 'desc');

        $new_service = $r_query->get();

        foreach($new_service as $item){
            array_push($new, $item);
        }

        return $new;
    }

    /**
     * 최근 한 달간 등록된 서비스의 서비스 목록을 얻는다.
     */
    public static function getNewServicesAll()
    {

        $new = [];
        $sort_new = [];
        $sort_id = [];
        $sort_query = \App\Models\Cms\SortDisplay::where('tab', 'new')->first();
        if($sort_query){
            $sort_new = explode(',',$sort_query->service_id);

            // $sort_display = \App\Models\Agent\Service::whereIn('id', $sort_new)->get();
            foreach($sort_new as $service_id){
                array_push($new, \App\Models\Agent\Service::find($service_id));
                array_push($sort_id, \App\Models\Agent\Service::find($service_id)->id);
            }
            // foreach($sort_display as $item){
            //     array_push($new, $item);
            //     array_push($sort_id, $item->id);
            // }
        }

        $r_query = \App\Models\Agent\Service::where('visible', 1)
        ->whereNotIn('process', [0, 1, 2, 5])
        ->whereNotIn('id', $sort_id)
        ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
        ->when(\Lang::getLocale() != "ko", function($query) {
            $query->whereHas('cat2', function($query) {
                $query->where('expo', 1);
            });
        })
        ->orderBy('created_at', 'desc');

        // return $r_query->get();
        $new_service = $r_query->get();

        foreach($new_service as $item){
            array_push($new, $item);
        }

        return $new;
    }

    public static function childCate($parent = 0, $selcates = "" )
    {
        $r_query = \App\Models\Agent\ServiceCategory::where('depth', 2);
        if($parent > 0)
        {
            $r_query->where('parent', $parent);
        }

        $filter_array = explode(",", $selcates);
        $filter_array =  array_values(array_filter(array_map('trim', $filter_array), 'strlen'));
        if(count($filter_array) > 0)
        {
            $r_query->whereIn('id', $filter_array);
        }

        $r_query->orderBy('parent')->orderBy('id');
        return $r_query->get();
    }

    public static function getCate($id)
    {
        return \App\Models\Agent\ServiceCategory::find($id);
    }

    public static function getFuncSearch($lang,  $ids="")
    {
        // $ratetbl = DB::table('tbl_agent_service as SVC')
        //             ->leftJoin('tbl_client_review AS RVW', 'SVC.id', '=', 'RVW.service_id')
        //             ->select('SVC.id as id', DB::raw('AVG(rating) as rating'))
        //            ->groupBy('service_id');
        // $svccount = DB::table('tbl_agent_service as SVC')
        //            ->leftJoin('tbl_client_service AS CS', 'SVC.id', '=', 'CS.service_id')
        //            ->select('SVC.id as id', DB::raw('COUNT(CS.service_id) as service_cnt'))
        //           ->groupBy('service_id');
        $r_query = \App\Models\Agent\Service::where('visible', 1)
        ->whereNotIn('process', [0, 1, 2, 5]);
        $r_query->where(function ($query) use ($lang) {
            $query->where('default_lang', $lang)->orWhere('lang', $lang);
        });
        // $r_query = DB::table('tbl_agent_service AS AS1')
                        // ->leftJoinSub($ratetbl, 'ratetbl', function ($join) {
                        //     $join->on('AS1.id', '=', 'ratetbl.id');
                        // })
                        // ->leftJoinSub($svccount, 'svccount', function ($join) {
                        //     $join->on('AS1.id', '=', 'svccount.id');
                        // })
                        // ->selectRaw('AS1.*, CT.'.\Lang::getLocale().' AS category, svccount.service_cnt AS service_cnt, ratetbl.rating as rating')
                        // ->leftJoin('tbl_service_category AS CT', 'CT.id', '=', 'AS1.category1')
                        // ->where('AS1.visible', '=', 1)
                        // ->whereNotIn('AS1.process', [0, 1, 2, 5]);



        if($ids)
        {

            $filter_array = explode(",", $ids);
            $filter_array =  array_values(array_filter(array_map('trim', $filter_array), 'strlen'));
            if(count($filter_array) > 0)
            {
                $r_query->whereIn('id', $filter_array);
            }
        }
        return $r_query->get();
    }

    public static function getSvcRating($id)
    {
        return DB::table('tbl_client_review')
                    ->select(DB::raw('AVG(rating) as rating'))
                    ->where('service_id', $id)
                   ->first()->rating;
    }

    public static function reqableMyService($serviceid, $clientid)
    {
        // $svccount = DB::table('tbl_client_service as CSV')
        //            ->leftJoin('tbl_client_site AS CST', 'CST.id', '=', 'CSV.site_id')
        //            ->where('CSV.service_id', $serviceid)
        //         //    ->select('CST.id as site_id', DB::raw('COUNT(CST.id) as site_cnt'))->groupBy('site_id');
        //             ->select('CST.id as site_id', DB::raw('(select COUNT(id) from tbl_client_site where process <> 5) as site_cnt'))->groupBy('site_id');

        // return \App\Models\Client\Site::where('tbl_client_site.client_id', $clientid)
        // ->leftJoinSub($svccount, 'svccount', function ($join) {
        //     $join->on('tbl_client_site.id', '=', 'svccount.site_id');
        // })->whereRaw('(svccount.site_cnt = 0 OR svccount.site_cnt is null)')->get();

        $service = \App\Models\Client\Service::select('site_id')
                    ->where('service_id', $serviceid)
                    ->where('client_id', $clientid)
                    ->where('process', '<>', 5)
                    ->get();

        $notin = [];
        if($service){
            foreach($service as $item){
                array_push($notin, $item->site_id);
            }
        }
        // dd($notin);
        return \App\Models\Client\Site::where('client_id', $clientid)
                ->whereNull('deleted_at')
                ->whereNotIn('id', $notin)
                ->get();
    }

}
