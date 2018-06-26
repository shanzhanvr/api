<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/26
 * Time: 10:47
 */

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Common\V1\Controllers\Area','middleware' => ['api.jwtauth']],function ($api) {
        $api->get('area/getarea', 'AreaController@getArea');
    });
});