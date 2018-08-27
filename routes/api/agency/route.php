<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Agency\V1\Controllers\Pay','middleware' => ['api.agency']],function ($api) {
        $api->POST('agency/pay/receiver', 'XubRechangeController@receivePay');//支付回调
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){
            $api->POST('agency/pay/receivefront', 'RechangePayController@checkOrder');//检查支付订单
            $api->POST('agency/pay/unified', 'RechangePayController@alipayPay');//下单
        });
    });
});