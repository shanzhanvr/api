<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 17:34
 */

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers\Auth','middleware' => ['api.vrauth']],function ($api) {
        $api->POST('refresh', 'AuthController@refresh');//刷新token
    });
});