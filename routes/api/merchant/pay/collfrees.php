<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/31
 * Time: 13:11
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Scenel\V1\Controllers\CollectFees','middleware' => ['api.merchant']],function ($api) {
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){
            $api->POST('collfree/checkIsPay', 'CollectFeesController@checkIsPay');//判断当前摄影全景功能是否收费
            $api->POST('collfree/scenelCollPay', 'CollectFeesController@scenelCollPay');//判断当前摄影全景功能是否收费

        });
    });
});