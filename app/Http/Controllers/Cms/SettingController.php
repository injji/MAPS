<?php

namespace App\Http\Controllers\Cms;

use Str;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Agent\StatExportController;
use App\Models\Agent\{Service, ServiceStat};
use App\Models\Cms\Notice;
use App\Models\Cms\QuestionOption;
use App\Models\Cms\SiteInfo;
use App\Models\Cms\SiteCard;
use App\Models\Cms\Terms;
use App\Models\Cms\Faq;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use View;
use DateTime;

class SettingController extends Controller
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
     * 관리자설정
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function admin(Request $request) {
        View::share('activeMenu', 'setting.admin');

        $menu_list = \App\Models\Cms\Menu::orderBy('id', 'asc')->get();
        $admin_list = \App\Models\AdminUser::orderBy('id', 'desc')->paginate(20);

        return view('cms.setting.admin', compact('menu_list', 'admin_list'));
    }

    public function get_menu(Request $request) {
        $menu = \App\Models\Cms\Menu::find($request->id);

        return response()->json([
            'code' => 200,
            'content' => $menu
        ]);
    }

    public function update_menu(Request $request) {
        $menu = \App\Models\Cms\Menu::find($request->id);

        $menu->read = $request->read;
        $menu->write = $request->write;
        $menu->save();

        return response()->json([
            'code' => 200
        ]);
    }

    public function update_admin_use(Request $request) {
        $admin = \App\Models\AdminUser::find($request->id);

        $admin->use = $request->use;
        $admin->save();

        return response()->json([
            'code' => 200
        ]);
    }

    public function get_permission_list(Request $request) {
        $list = \App\Models\Cms\MenuPermission::where('user_id', $request->id)
                                            ->orderBy('menu_id', 'asc')
                                            ->get();

        return response()->json([
            'code' => 200,
            'list' => $list
        ]);
    }

    public function save_permission(Request $request) {
        $admin = \App\Models\AdminUser::find($request->user_id);
        $admin->level = $request->level;
        $admin->save();

        if($request->menu_id_list != '')
        {
            $menu_id_arr = explode(',', $request->menu_id_list);
            $menu_level_arr = explode(',', $request->menu_level_list);

            foreach ($menu_id_arr as $key => $value)
            {
                $permission = \App\Models\Cms\MenuPermission::where('user_id', $request->user_id)->where('menu_id', $value)->first();

                if ($permission)
                {
                    if ($menu_level_arr[$key] == '') {
                        $permission->delete();
                    }
                    else {
                        $permission->user_id = $request->user_id;
                        $permission->menu_id = $value;
                        $permission->level = $menu_level_arr[$key];
                        $permission->save();
                    }
                }
                else
                {
                    if ($menu_level_arr[$key] != '') {
                        $permission = new \App\Models\Cms\MenuPermission();

                        $permission->user_id = $request->user_id;
                        $permission->menu_id = $value;
                        $permission->level = $menu_level_arr[$key];
                        $permission->save();
                    }
                }
            }
        }

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * 홈페이지문의
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function question(Request $request) {
        View::share('activeMenu', 'setting.question');

        $data_range = $request->data_range;
        $datarange  = explode(' ~ ', $data_range);
        $st_date = isset($datarange[0]) ? $datarange[0] : '';
        $ed_date = isset($datarange[1]) ? $datarange[1] : '';
        $keyword = $request->keyword ?? '';
        $sort    = $request->sort ?? '1';

        if(!$st_date || !$ed_date) {
            $stdate = new DateTime();
            $stdate->modify('-90 day');
            $prev_date = $stdate->format('Y-m-d');

            $st_date = $prev_date;
            $ed_date = date('Y-m-d');
        }

        return view('cms.setting.question', compact( 'st_date', 'ed_date' ));
    }

    /**
     * 공지사항
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function notice(Request $request) {
        View::share('activeMenu', 'setting.notice');

        $keyword = $request->keyword ?? '';
        $type    = $request->type ?? -1;

        $results = Notice::
                        where(function($q) use ($type) {
                            if($type >= 0) {
                                $q->where('type', $type);
                            }
                        })
                        ->where(function($q) use ($keyword) {
                            if($keyword) {
                                $q->where('title', 'like', '%'.$keyword.'%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->paginate(20);

        $write_permission = \Auth::guard('cms')->user()->getWritePermission(23);

        return view('cms.setting.notice', compact( 'results', 'keyword', 'type', 'write_permission' ));
    }
    public function notice_register(Request $request) {
        View::share('activeMenu', 'setting.notice');

        return view('cms.setting.notice_register');
    }
    public function notice_detail(Request $request) {
        View::share('activeMenu', 'setting.notice');

        $id = $request->id;
        if(!$id) {
            return redirect('setting/notice');
        }
        $result = Notice::find($id);
        return view('cms.setting.notice_detail', compact('result'));
    }
    /**
     * notice 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @return array
     */
    public function changeNotice(Request $request)
    {
        $note_id = $request->id;
        $type    = $request->type;
        $popup   = $request->popup;
        $title   = $request->title;
        $content = $request->content;

        if($note_id) {   // 저장
            $notice = Notice::find($note_id);
        }
        else {  // add
            $notice = new Notice();
        }
        $notice->type     = $type;
        $notice->popup    = $popup;
        $notice->title    = $title;
        $notice->content  = $content;

        if ($request->hasFile('thumb_img')) {
            $img = $request->thumb_img->store('store', 'public');
            $notice->img  = $img;

        }

        $notice->save();

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * notice 삭제
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function deleteNotice(Request $request)
    {
        $id = $request->id;

        $faq = Notice::find($id);
        $faq->delete();

        return response()->json([
            'code' => 200
        ]);
    }


    /**
     * FAQ
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function faq_set(Request $request) {
        View::share('activeMenu', 'setting.faq_set');

        $faq = Faq::orderBy('order', 'asc')->get();

        return view('cms.setting.faq_set', compact( 'faq' ));
    }


    /**
     * 사이트관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function site(Request $request) {
        View::share('activeMenu', 'setting.site');

        $q_options = QuestionOption::get();
        $site_info = SiteInfo::first();
        $site_card = SiteCard::get();
        $write_permission = \Auth::guard('cms')->user()->getWritePermission(24);

        return view('cms.setting.site', compact('q_options', 'site_info', 'site_card', 'write_permission'));
    }
    /**
     * site info 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @return array
     */
    public function changeSiteInfo(Request $request)
    {
        $type = $request->type;
        if($type == 'delete') {     // card delete
            SiteCard::find($request->id)->delete();
        }
        else {
            $option_id = $request->option_id;
            //
            $question_option = QuestionOption::where('id', $option_id)->first();
            $question_option->fill($request->only('content'));
            $question_option->save();

            $site_info = SiteInfo::first();
            $site_info->fill($request->except('content'));
            $site_info->save();

            // payment card
            $site_card = $request->site_card;
            if($site_card) {
                $site_card = json_decode($site_card);
                $activeIdx = $request->active;

                foreach ($site_card as $key => $value) {
                    if(!isset($value)) continue;
                    $value = (object)$value;
                    $siteCard = new SiteCard();
                    $siteCard->bank     = $value->bank;
                    $siteCard->account  = $value->account;
                    $siteCard->owner    = $value->owner;
                    $siteCard->save();
                }

                SiteCard::query()->update( ['active' => 0] );
                $siteCard = SiteCard::find($activeIdx);
                if($siteCard) {
                    $siteCard->active = 1;
                    $siteCard->save();
                }
            }
        }

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * 약관관리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function term(Request $request) {
        View::share('activeMenu', 'setting.term');

        $terms = Terms::get();
        $write_permission = \Auth::guard('cms')->user()->getWritePermission(25);

        return view('cms.setting.term', compact('terms', 'write_permission'));
    }
    /**
     * 약관 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @return array
     */
    public function changeTerm(Request $request)
    {
        //
        $content1 = $request->content1;
        $content2 = $request->content2;
        $content3 = $request->content3;

        if($content1) {
            $term = Terms::find(1);
            $term->content = $content1;
            $term->save();
        }
        if($content2) {
            $term = Terms::find(2);
            $term->content = $content2;
            $term->save();
        }
        if($content3) {
            $term = Terms::find(3);
            $term->content = $content3;
            $term->save();
        }

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * FAQ 등록
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function faq(Request $request)
    {
        $question = $request->question;
        $answer = $request->answer;
        $faq_category = $request->faq_category;

        $faqCnt = Faq::count();

        $faq = new Faq();
        $faq->faq_category = $faq_category;
        $faq->question = $question;
        $faq->answer = $answer;
        $faq->order = $faqCnt+1;
        $faq->save();

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * FAQ 수정
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function faqUpdate(Request $request)
    {
        $id = $request->id;
        $question = $request->question;
        $answer = $request->answer;
        $faq_category = $request->faq_category;

        $faq = Faq::find($id);
        $faq->faq_category = $faq_category;
        $faq->question = $question;
        $faq->answer = $answer;
        $faq->save();

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * FAQ 삭제
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function faqDelete(Request $request)
    {
        $id = $request->id;

        $faq = Faq::find($id);
        $faq->delete();

        return response()->json([
            'code' => 200
        ]);
    }

    /**
     * FAQ 순서변경
     *
     * @param Illuminate\Http\Request $request
     * @return array
     */
    public function faqOrderUpdate(Request $request)
    {
        $faq_id = explode(',',$request->faq_id);

        foreach($faq_id as $key => $item){
            $faq = Faq::find($item);
            $faq->order = $key+1;
            $faq->save();
        }

        return response()->json([
            'code' => 200,
            'faq_id' => $faq_id,
        ]);
    }

    /**
     * 스크립트 설치 요청
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function script_set(Request $request) {
        View::share('activeMenu', 'setting.script_set');

        $script = \App\Models\Client\ScriptRequest::orderBy('id','desc')->paginate(20);

        return view('cms.setting.script_set', compact('script'));
    }

    /**
     * 스크립트 설치 완료 처리
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function script_request(Request $request) {

        $script = \App\Models\Client\ScriptRequest::find($request->id)->update(['flag' => 1]);

        return response()->json([
            'code' => 200,
            'script' => $script
        ]);
    }

}
