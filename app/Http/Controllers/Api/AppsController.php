<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Agent\Service;
use App\Models\Passport\Client;
use App\Models\Api\AccessToken;
use App\Models\Api\Script;
use Illuminate\Support\Str;

class AppsController extends Controller{

    public $today;

    public function __construct()
    {
        $this->today = date('Y-m-d H:i:s');
    }

    //개별 푸시 발송요청
    public function notification(Request $request)
    {

    }

    //랜딩뷰 추천상품
    public function landing(Request $request)
    {

    }

    //리타겟 추천상품
    public function retarget(Request $request)
    {

    }
}
?>
