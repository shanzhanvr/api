<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/8/28
 * Time: 10:57
 */

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Agency\V1\Controllers\Auth','middleware' => ['api.agency']],function ($api) {
        //商户登录注册接口无需验证token
        $api->post('agency/auth/login', 'AuthController@login');
        $api->post('agency/auth/register', 'AuthController@register');
        $api->group(['middleware' => ['before' => 'jwt.auth']], function ($api){//之前验证token
            $api->post('agency/auth/users', 'AuthController@getUsers');

        });
    });
});