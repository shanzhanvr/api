<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 13:08
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Common\V1\Controllers\Pay','middleware' => ['api.merchant']],function ($api) {
        $api->POST('pay/receiver', 'LeanTongBaoPayController@receivePay');//支付回调
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){
            $api->POST('pay/receivefront', 'LeanTongBaoPayController@checkOrder');//检查支付订单
            $api->POST('pay/unified', 'LeanTongBaoPayController@alipayPay');//下单
        });
    });
});