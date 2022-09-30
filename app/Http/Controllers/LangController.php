<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

class LangController extends Controller
{
    /**
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        
    }

    /**
     * 클라이언트 언어 변경
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang' => 'required',            
        ]);

        if (!$validator->passes())
            return response()->json(['code' => 401, 'error' => $validator->errors()]);

        \App::setLocale($request->lang);

        if ($request->user()) {
            $request->user()->lang = $request->lang;
            $request->user()->save();    
        } else {
            session()->put('store_lang', $request->lang);
        }
        
        return response()->json(['code' => 200, 'message' => __('messages.change')]);
    }
}
