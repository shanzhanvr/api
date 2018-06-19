<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/14
 * Time: 9:45
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers\Sms','middleware' => ['api.vrauth']],function ($api) {
        $api->get('sms/smssend', 'SmsController@smsSend');//短信发送 验证token
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){

        });
    });
});