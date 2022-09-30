<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
//CCC 20220615
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'register/client_save',
        'register/agent_save',
        'register/byapps',
        'login/check_account',
        'login/reset_pw',
        'login',
        'lang/change',
        'site/create',
        'password/check',
        'my/store',
        'service/create',
        'service/store',
        'reqsvcpay',
        'mreqsvcpay',
        'reqextendpay',
        'kcp_api_trade_reg',
        'order_mobile',
        'getorderno',
        'order/payment/payment'
    ];
}
