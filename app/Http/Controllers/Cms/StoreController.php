<?php

namespace App\Http\Controllers\Cms;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Cms\Store;
use App\Models\Cms\FuncKind;
use App\Models\Agent\Service;
use App\Models\Agent\ServiceCategory;
use Illuminate\Http\Request;
use View;
use DateTime;
use App\Models\{Store\Store as StoreStore};
use App\Models\Cms\Preview;
use App\Models\Cms\Content;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    /***/
    public function __construct()
    {
    }

    /**
     *
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {

    }

    /**
     * 배너관리 view
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function banner(Request $request) {
        View::share('activeMenu', 'store.banner');

        $tab     = $request->tab ?? 1;
        $results = Store::getBanners($tab);
        $write_permission = \Auth::guard('cms')->user()->getWritePermission(20);

        return view('cms.store.banner', compact( 'results', 'tab', 'write_permission'));
    }

    /**
     * get banner by id
     *
     * @param Illuminate\Http\Request $request
     */
    public function getBannerById(Request $request)
    {
        $banner_id = $request->id;

        $result = Store::find($banner_id);

        return response()->json([
            'code' => 200,
            'data' => $result
        ]);
    }

    /**
     * banner 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @param App\Models\Client\Site $site
     * @return array
     */
    public function changeBanner(Request $request)
    {
        $type    = $request->type;
        $items   = $request->items;
        $banner_id = $request->banner_id;
        $status   = $request->status;
        $title    = $request->title;
        $url      = $request->url;
        $st_date  = $request->st_date;
        $end_date = $request->end_date;
        $banner_lang = $request->banner_lang;
        $banner_type = $request->banner_type;

        if($type == 'save') {
            if($banner_lang) {  // only image
                if ($request->hasFile('banner_img')) {
                    $banner = Store::find($banner_id);
                    $img = $request->banner_img->store('store', 'public');
                    if($banner_lang == 'ko') {
                        $banner->ko = $img;
                    }
                    else if($banner_lang == 'en') {
                        $banner->en = $img;
                    }

                }
                $banner->save();
            }
            else {
                if($banner_id) {   // 저장
                    $banner = Store::find($banner_id);
                }
                else {  // add
                    $banner = new Store();
                    $banner->sort = 99;
                    $banner->type = $banner_type;
                }
                $banner->status   = $status;
                $banner->title    = $title;
                $banner->url      = $url;
                $banner->st_date  = $st_date;
                $banner->end_date = $end_date;

                if ($request->hasFile('banner_img'))
                    $banner->ko = $request->banner_img->store('store', 'public');

                $banner->save();
            }

        }
        else if($type == 'status') {
            $banner = Store::find($banner_id);
            $banner->status  = $status;

            $banner->save();
        }
        else if($type == 'date') {
            $banner = Store::find($banner_id);
            $banner->st_date  = $st_date;
            $banner->end_date = $end_date;

            $banner->save();
        }
        else if($type == 'sort') {
            foreach ($items as $key => $item) {
                $item = (object)$item;
                $banner = Store::find($item->id);
                $banner->sort = $item->sort;
                $banner->save();
            }
        }
        else if($type == 'delete') {
            $banner = Store::find($banner_id);
            $banner->delete();
        }

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * 기능별분류
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function func(Request $request) {
        View::share('activeMenu', 'store.func');

        $keyword = $request->keyword ?? '';
        $results = FuncKind::getFuncKind($keyword);
        $write_permission = \Auth::guard('cms')->user()->getWritePermission(21);

        return view('cms.store.func', compact( 'results', 'keyword', 'write_permission'));
    }

    public function func_register(Request $request) {
        View::share('activeMenu', 'store.func');

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        return view('cms.store.func_register', compact('agent_list', 'service_list', 'category_list'));
    }

    public function func_detail(Request $request) {
        View::share('activeMenu', 'store.func');

        $id = $request->id;
        if(!$id) {
            return redirect('cms_store/func');
        }

        $agent_list = \App\Models\Users::where('type', 2)->orderBy('id','desc')->get();
        $service_list = Service::orderBy('id','desc')->get();
        $category_list = ServiceCategory::getCate();

        $results = FuncKind::getFuncKindById($id);
        $result  = $results[0];
        // 서비스 list
        $service_lists = array();
        if($result->service) {
            $service_lists = Service::whereRaw('FIND_IN_SET(id, "'.$result->service.'")')->get();
        }

        return view('cms.store.func_detail', compact('result', 'agent_list', 'service_list', 'category_list', 'service_lists'));
    }

    /**
     * 컨텐츠
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */

    public function conte(Request $request) {
        View::share('activeMenu', 'store.conte');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $banner = $request->banner ?? '';
        $keyword = $request->keyword ?? '';

        if (!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        $results = Content::whereBetween('created_at', [$st_date, $ed_date.' 23:59:59']);
        if($keyword){
            $results->where('title', 'LIKE', '%'.$keyword.'%');
        }
        if($banner){
            $results->where('banner', $banner);
        }
        $results = $results->orderBy('order','asc')->get();
        $write_permission = \Auth::guard('cms')->user()->getWritePermission(21);

        return view('cms.store.conte', compact( 'results', 'st_date', 'ed_date', 'banner', 'keyword', 'write_permission'));
    }

    public function conte_register(Request $request) {
        View::share('activeMenu', 'store.conte');

        $id = $request->id;
        $content = [];
        if($id){
            $content = Content::find($id);
        }

        return view('cms.store.conte_register', compact('content'));
    }

    public function conte_preview(Request $request) {

        $title = $request->title;
        $description = $request->description;
        $content = $request->content;
        $thumb_img = $request->thumb_img;
        $preview_img = $request->preview_img;

        if($thumb_img == 'null') $thumb_img = null;

        if ($thumb_img) {

            $preview = new Preview();
            $img = $thumb_img->store('preview', 'public');
            $preview->img  = $img;

            $preview->save();
            $request->session()->put('preview_image', Storage::url($img));

        }else{
            $request->session()->put('preview_image', $preview_img);
        }

        $request->session()->put('preview_title', $title);
        $request->session()->put('preview_description', $description);
        $request->session()->put('preview_content', $content);

        return response()->json([
            'code' => 200,
        ]);
    }

    /**
      * MAPS CONTENT 저장
      *
      * @param Illuminate\Http\Request $request
    */
    public function conte_store (Request $request)
    {

        $id = $request->id;
        $title = $request->title;
        $description = $request->description;
        $content = $request->content;
        $thumb_img = $request->thumb_img;
        $banner = $request->banner;

        if($thumb_img == 'null') $thumb_img = null;

        $Content_cnt = Content::count();
        if($id){
            $store_content = Content::find($id);
        }else{
            $store_content = new Content();
            $store_content->order = $Content_cnt+1;
        }

        if ($thumb_img) {
            $img = $thumb_img->store('content', 'public');
            $store_content->img  = $img;
        }

        $store_content->title = $title;
        $store_content->description = $description;
        $store_content->content = $content;
        $store_content->banner = $banner;

        $store_content->save();

        //미리보기 데이터 삭제
        $previews = Preview::get();
        foreach($previews as $preview){
            if(is_file(storage_path('app/public/'.$preview->img))){
                unlink(storage_path('app/public/'.$preview->img));
            }
        }

        Preview::query()->delete();

        return response()->json([
            'code' => 200,
            'test' => $thumb_img
        ]);
    }

    /**
      * MAPS CONTENT 삭제
      *
      * @param Illuminate\Http\Request $request
    */
    public function conte_delete (Request $request)
    {

        $id = $request->id;

        Content::find($id)->delete();

        return response()->json([
            'code' => 200,
        ]);
    }

    /**
      * MAPS CONTENT 순서변경
      *
      * @param Illuminate\Http\Request $request
    */
    public function conte_sort (Request $request)
    {

        $conte_arr = explode(',', $request->conte_arr);

        foreach($conte_arr as $key => $id){
            Content::find($id)->update(['order' => $key+1]);
        }

        return response()->json([
            'code' => 200,
            'arr' => $conte_arr
        ]);
    }

    /**
      * MAPS CONTENT_PREVIEW 페이지
      *
      * @param Illuminate\Http\Request $request
    */
    public function mapscontentpreview (Request $request)
    {

        $title = $request->session()->get('preview_title');
        $description = $request->session()->get('preview_description');
        $content = $request->session()->get('preview_content');
        $today = date('Y.m.d');

        $preview_img = $request->session()->get('preview_image');

        $viewdata = [
            'categorys' => ServiceCategory::getCate(),
            'funclists' => StoreStore::getFuncKind(),
            'title' => $title ?? '',
            'description' => $description ?? '',
            'content' => $content ?? '',
            'thumb_img' => $preview_img ?? '',
            'today' => $today,
        ];

        return view('cms.store.maps_content_preview', $viewdata);
    }


    /**
     * get service data
     *
     * @param Illuminate\Http\Request $request
     */
    public function getService(Request $request)
    {
        $type       = $request->type;
        $service    = $request->service ?? '';
        $agent_id   = $request->agent_id ?? 0;
        $service_id = $request->service_id ?? 0;
        $category_id = $request->category_id ?? 0;

        if($type == 'service') {
            $result = Service::
                            selectRaw('*, (SELECT U.company_name FROM tbl_users AS U WHERE U.id=agent_id) AS company_name,
                                    (SELECT C.ko FROM tbl_service_category AS C WHERE C.id=category1) AS cat1,
                                    (SELECT C.ko FROM tbl_service_category AS C WHERE C.id=category2) AS cat2')
                            ->whereRaw('FIND_IN_SET(id, "'.$service.'")')
                            ->get();
        }
        else {
            $result = Service::
                            selectRaw('*, (SELECT U.company_name FROM tbl_users AS U WHERE U.id=agent_id) AS company_name,
                                    (SELECT C.ko FROM tbl_service_category AS C WHERE C.id=category1) AS cat1,
                                    (SELECT C.ko FROM tbl_service_category AS C WHERE C.id=category2) AS cat2')
                            ->where(function($q) use ($agent_id) {
                                if($agent_id > 0) {
                                    $q->where('agent_id', $agent_id);
                                }
                            })
                            ->where(function($q) use ($service_id) {
                                if($service_id > 0) {
                                    $q->where('id', $service_id);
                                }
                            })
                            ->where(function($q) use ($category_id) {
                                if($category_id > 0) {
                                    $q->where('category1', $category_id);
                                }
                            })
                            ->get();
        }

        return response()->json([
            'code' => 200,
            'data' => $result
        ]);
    }
    /**
     * func kind 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @return array
     */
    public function changeFuncKind(Request $request)
    {
        $type    = $request->type;
        $func_id = $request->func_id;
        $expo    = $request->expo;
        $service = $request->service;

        $weight   = $request->weight;
        $title    = $request->title;
        $kind     = $request->kind;
        $st_date  = $request->st_date;
        $end_date = $request->end_date;
        $service  = $request->service;

        if($type == 'save') {
            if($func_id) {   // 저장
                $func_kind = FuncKind::find($func_id);
            }
            else {  // add
                $func_kind = new FuncKind();
            }
            $func_kind->weight   = $weight;
            $func_kind->title    = $title;
            $func_kind->kind     = $kind;
            $func_kind->st_date  = $st_date;
            $func_kind->end_date = $end_date;
            $func_kind->service = $service;

            if ($request->hasFile('icon'))
                $func_kind->icon = $request->icon->store('store', 'public');

            if ($request->hasFile('thumb'))
                $func_kind->thumb = $request->thumb->store('store', 'public');

            $func_kind->save();

        }
        else if($type == 'service') {
            $func_kind = FuncKind::find($func_id);
            $func_kind->service = $service;
            $func_kind->save();
        }
        else if($type == 'expo') {
            $func_kind = FuncKind::find($func_id);
            $func_kind->expo = $expo;
            $func_kind->save();
        }
        else if($type == 'delete') {
            $func_kind = FuncKind::find($func_id);
            $func_kind->delete();
        }

        return response()->json([
            'code' => 200
        ]);
    }

}
