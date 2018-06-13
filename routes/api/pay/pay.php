<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 13:08
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers\Pay','middleware' => ['api.vrauth']],function ($api) {
        $api->POST('pay/receiver', 'ReceiveController@receivePay');//支付回调
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){ //验证token
            $api->POST('pay/unified', 'ReceiveController@alipayPay');//下单
            $api->POST('pay/receivefront', 'ReceiveController@checkOrder');//下单
        });
    });
});