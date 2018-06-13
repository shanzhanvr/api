<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 16:58
 */

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\V1\Controllers\Merchant','middleware' => ['api.vrauth']],function ($api) {
        $api->post('merchant/login', 'MerchantController@login');
        $api->post('merchant/register', 'MerchantController@register');
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){//之前验证token
            //获取资源路由
            $api->get('merchant/getuserinfo', 'MerchantController@getMerchantInfo');
            $api->get('merchant/rechange', 'MerchantController@getMerchantList');
        });
    });
});