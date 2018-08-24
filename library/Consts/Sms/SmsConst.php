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
    const ACCESSKEYID = 'LTAIvBU9IHacSpvx';
    //秘钥
    const ACCESSKEYSECRET =  'HzQgVNq1IhWObQ54PwMkhhCFGvuLJP';
    //地址
    const SENDMESSAGEURL = 'dysmsapi.aliyuncs.com';
    //签名
    const SIGNNAME = '闪展';

    //短信登陆注册
    const REGISTER_CERTIFICATION = '1';

    //信息变更
    const MESSAGEUPDATE_CERTIFICATION = '2';


    //身份信息认证
    const IDCARD_CERTIFICATION = '3';

    const  IDCARD_CERTIFICATION_DESC= 'SMS_140070301'; //身份验证

    const REGISTER_CERTIFICATION_DESC = 'SMS_140015247';//会员注册

    const MESSAGEUPDATE_CERTIFICATION_DESC = 'SMS_140035284';//信息变更

    const MEMCACHECAPTCHA_KEY = 'SHANZHANVR_CAPTCHA';

    public static function getMessageTemplateCode(){
        return [
            self::REGISTER_CERTIFICATION=>self::REGISTER_CERTIFICATION_DESC,
            self::MESSAGEUPDATE_CERTIFICATION=>self::MESSAGEUPDATE_CERTIFICATION_DESC,
            self::IDCARD_CERTIFICATION=>self::IDCARD_CERTIFICATION_DESC,
        ];
    }
    public static function getMessageTemplateCodeItem($item){
        return array_get(self::getMessageTemplateCode(),$item);
    }
}