<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/2
 * Time: 10:34
 */
namespace App\Api\Common\V1\Controllers\Speech;
use Illuminate\Support\Facades\Input;
use library\Response\JsonResponse;
use library\Service\Speech\HttpSpeech;

class SpeechController{

    public function langToVido(){
//        try{
            $text = Input::get('text');
            if(empty($text)){
                return JsonResponse::error(0,'文字内容不能为空');
            }
            $clinet = HttpSpeech::getInstance();
            return $clinet->languageToSpeech($text);
//        }catch (\Exception $e){
//            return JsonResponse::error(0,'网络错误!请稍后重试');
//        }
    }
}