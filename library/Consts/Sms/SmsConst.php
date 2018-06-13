<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/5/24
 * Time: 13:10
 */
namespace library\Service\Contst\Sms;
class SmsConst{

    //短信验证ID
    const ACCESSKEYID = 'LTAIlQPsjjPfOMxo';
    //秘钥
    const ACCESSKEYSECRET =  'QBaqybcsuGluxvi1F95DWloykosMen';
    //地址
    const SENDMESSAGEURL = 'dysmsapi.aliyuncs.com';
    //签名
    const SIGNNAME = '闪展';
    //信息变更
    const MESSAGEUPDATE_CERTIFICATION = '1';
    //短信登陆注册
    const REGISTER_CERTIFICATION = '2';
    //身份信息认证
    const IDCARD_CERTIFICATION = '3';

    const MESSAGEUPDATE_CERTIFICATION_DESC = 'SMS_136025376';

    const REGISTER_CERTIFICATION_DESC = 'SMS_136045392';

    const IDCARD_CERTIFICATION_DESC = 'SMS_135793091';

    const MEMCACHECAPTCHA_KEY = 'SHANZHANVR_CAPTCHA';

    public static function getMessageTemplateCode(){

        return [
            self::MESSAGEUPDATE_CERTIFICATION=>self::MESSAGEUPDATE_CERTIFICATION_DESC,
            self::REGISTER_CERTIFICATION=>self::REGISTER_CERTIFICATION_DESC,
            self::IDCARD_CERTIFICATION=>self::IDCARD_CERTIFICATION_DESC,
        ];
    }
    public static function getMessageTemplateCodeItem($item){
        return array_get(self::getMessageTemplateCode(),$item);
    }
}