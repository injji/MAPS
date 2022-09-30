<?php

namespace App\Http\Controllers\Cms;

use Str;
use App\Http\Controllers\Controller;
use App\Models\Agent\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use View;
use Validator;
use DateTime;

class CategoryController extends Controller
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
     * category
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function list(Request $request) {
        View::share('activeMenu', 'category.service');
        $tab = $request->tab ?? 1;
        $cat_id = $request->cat_id ?? '';
        $main_cat = ServiceCategory::where('depth', 1)->orderBy('sort', 'ASC')->get();
        $sub_cat  = ServiceCategory::where('depth', 2)->orderBy('sort', 'ASC')->get();
        $write_permission = \Auth::guard('cms')->user()->getWritePermission(10);

        return view('cms.category.list', compact('main_cat', 'sub_cat', 'tab', 'cat_id', 'write_permission'));
    }
    /**
     * category 수정
     *
     * @param Illuminate\Http\Request $request
     * @param App\Models\Agent\Service $service
     * @param App\Models\Client\Site $site
     * @return array
     */
    public function changeCategory(Request $request)
    {
        $type    = $request->type;
        $items   = $request->items;
        $category_id = $request->category_id;
        $content     = $request->content;
        $cat_id      = $request->cat_id;
        $expo        = $request->expo;

        if($type == 'content') {
            if($category_id) {   // 저장
                $category = ServiceCategory::find($category_id);
                $category->ko      = $content;
                $category->save();
            }
            else {  // add
                $category = new ServiceCategory();
                $category->ko      = $content;
                $category->depth   = $cat_id ? 2 : 1;
                $category->parent  = $cat_id ?? 0;
                $category->sort    = 99;
                $category->save();
            }
        }
        else if($type == 'sort') {
            foreach ($items as $key => $item) {
                $item = (object)$item;
                $category = ServiceCategory::find($item->id);
                $category->sort = $item->sort;
                $category->save();
            }
        }
        else if($type == 'expo') {
            $category = ServiceCategory::find($category_id);
            $category->expo = $expo;
            $category->save();
        }
        else if($type == 'delete') {
            $category = ServiceCategory::find($cat_id);
            $category->delete();
        }
        else if($type == 'other_lang') {
            foreach ($items as $key => $item) {
                $item = (object)$item;
                $category = ServiceCategory::find($item->id);
                $category->en = $item->cont;
                $category->save();
            }
        }

        return response()->json([
            'code' => 200
        ]);
    }

}
