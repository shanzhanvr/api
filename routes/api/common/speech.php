<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/2
 * Time: 10:43
 */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Common\V1\Controllers\Speech','middleware' => ['api.jwtauth']],function ($api) {
        $api->post('speech/langtovido', 'SpeechController@langToVido');
    });
});