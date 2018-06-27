<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/14
 * Time: 9:37
 */
namespace App\Api\Common\V1\Controllers\Sms;
use App\Api\Common\V1\Bls\Model\Logsms;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use library\Response\JsonResponse;
use library\Service\Cache\TMRedisCacheMgr;
use library\Service\Contst\Sms\SmsConst;
use library\Service\Sms\Sms;

class SmsController extends Controller{

    public function smsSend(){
        $sms = Sms::getInstance();
        Log::info(json_encode(Input::all()));
        //判断手机号
        if(!Input::get('mobile') || !\helper::isMobile(Input::get('mobile'))){
            return JsonResponse::error(0,'请输入正确的手机号');
        }
        if(empty(Input::get('action')) || !SmsConst::getMessageTemplateCodeItem(Input::get('action'))){
            return JsonResponse::error(0,'参数不正确');
        }
        $redis = TMRedisCacheMgr::getInstance();
        $mobile = Input::get('mobile');
        //调用枷锁服务
        if(!$redis->setLock($mobile,2)){
            return JsonResponse::error(0,'请勿频繁访问');
        }
        $code = unserialize($redis->get(Input::get('mobile')));
        if($code){
            return JsonResponse::error(0,'60秒内请勿重复获取验证码');
        }
        $result = $sms->request(Input::get('mobile'),Input::get('action'));
        $redis->setex(Input::get('mobile'),serialize($result['Code']),60);
        Logsms::create(['smsPhoneNumber'=>$mobile,'smsReturnCode'=>'====','smsCode'=>$result['outId'],'ip'=>\helper::getClientIp()]);
        return JsonResponse::success(['Message'=>$result['Message'],'Code'=>$result['Code'],'smsCode'=>$result['outId']]);
    }
}