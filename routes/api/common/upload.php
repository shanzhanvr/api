<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/22
 * Time: 13:32
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Common\V1\Controllers\Upload','middleware' => ['api.jwtauth']],function ($api) {
        $api->POST('image/upload', 'UploadController@upFile');//图片上传
    });
});