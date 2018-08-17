<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/31
 * Time: 13:11
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Pay\V1\Controllers\Flicker','middleware' => ['api.merchant']],function ($api) {
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){
            $api->POST('flicker/checkIsPay', 'FlickerController@checkIsPay');//判断当前摄影全景功能是否收费
            $api->POST('flicker/scenelCollPay', 'FlickerController@scenelCollPay');//全景功能支付

        });
    });
});