<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/28
 * Time: 19:22
 */


$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Panorama\V1\Controller','middleware' => ['api.merchant']],function ($api) {
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){//之前验证token
            $api->POST('panorama/buckle', 'BucklePayController@buckle');//扣币
        });
    });
});