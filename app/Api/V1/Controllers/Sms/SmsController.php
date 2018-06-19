<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/14
 * Time: 9:37
 */
namespace App\Api\V1\Controllers\Sms;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use library\Response\JsonResponse;
use library\Service\Cache\TMRedisCacheMgr;
use library\Service\Contst\Sms\SmsConst;
use library\Service\Sms\Sms;

class SmsController extends Controller{

    public function smsSend(Request $request){
        $sms = Sms::getInstance();
        //判断手机号
        if(!Input::get('mobile') || !\helper::isMobile(Input::get('mobile'))){
            return JsonResponse::error(0,'请输入正确的手机号');
        }
        if(empty(Input::get('action')) || !SmsConst::getMessageTemplateCodeItem(Input::get('action'))){
            return JsonResponse::error(0,'参数不正确');
        }
        $result = $sms->request(Input::get('mobile'),Input::get('action'));
        return JsonResponse::success(['Message'=>$result['Message'],'Code'=>$result['Code']]);
    }
}