<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 16:58
 */

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Merchant\V1\Controllers\Merchant','middleware' => ['api.merchant']],function ($api) {
        //商户登录注册接口无需验证token
        $api->post('merchant/login', 'MerchantController@login');
        $api->post('merchant/register', 'MerchantController@register');
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){//之前验证token
            $api->get('merchant/getmerchant', 'MerchantController@getMerchantInfo');//获取商户详细信息
            $api->get('merchant/rechange', 'MerchantController@getRechangeList');//获取商户充值列表
        });
    });
});