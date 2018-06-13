<?php
/*
 * 此文件用于验证短信服务API接口，供开发时参考
 * 执行验证前请确保文件为utf-8编码，并替换相应参数为您自己的信息，并取消相关调用的注释
 * 建议验证前先执行Test.php验证PHP环境
 *
 * 2017/11/30
 */
namespace library\Service\ApiSendMessage;
use library\Service\Contst\Api\SendMessageApiConst;
use library\Service\ApiSendMessage\SignatureHelper;
use Mockery\Exception;

/**
 * 发送短信
 */

class SendMessage {

   public  static function sendSm($phone,$num) {
        $params = array ();
        // *** 需用户填写部分 ***
        $params["PhoneNumbers"] =$phone;
        $params["SignName"] = SendMessageApiConst::SIGNNAME;
       $params["TemplateCode"] = SendMessageApiConst::getMessageTemplateCodeItem($num);
        $params['TemplateParam'] = Array (
            "code" => rand(100000,999999)
        );
        $params['OutId'] = rand(10000,99999);
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();
        // 此处可能会抛出异常，注意catch
       $result = $helper->request(
           SendMessageApiConst::ACCESSKEYID,
           SendMessageApiConst::ACCESSKEYSECRET,
           SendMessageApiConst::SENDMESSAGEURL,
           array_merge($params, array(
                   "RegionId" => "cn-hangzhou",
                   "Action" => "SendSms",
                   "Version" => "2017-05-25",))
            );
            $result = \helper::object2array($result);
            if($result['Message'] == 'OK' && $result['Code'] =='OK'){
                return $params['TemplateParam'];
            }else{
                return false;
            }
    }
}




