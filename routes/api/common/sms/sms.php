<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/14
 * Time: 9:45
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Common\V1\Controllers\Sms',],function ($api) {
        $api->get('sms/smssend', 'SmsController@smsSend');
    });
});