<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Storage;

class TempFileController extends Controller
{
    /**
     * @param Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:front')->only('__invoke');
    }

    /**
     * 임시파일 업로드
     *
     * @param \Illuminate\Http\Request  $request
     * @return string
     */
    public function upload(Request $request)
    {
        $request->validate([
            'uploadfile' => 'required|file',
        ]);

        return Storage::disk('public')->url($request->uploadfile->store('temp/upload/'.now()->format('ymdH'), 'public'));
    }
}
