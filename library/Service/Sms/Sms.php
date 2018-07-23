<?php
/*
 * 此文件用于验证短信服务API接口，供开发时参考
 * 执行验证前请确保文件为utf-8编码，并替换相应参数为您自己的信息，并取消相关调用的注释
 * 建议验证前先执行Test.php验证PHP环境
 *
 * 2017/11/30
 */
namespace library\Service\Sms;
use library\Service\Contst\Sms\SmsConst;
use Mockery\Exception;

/**
 * 发送短信
 */

class Sms implements Contract {

    protected $phone;

    public $num;
    /**
     *
     * @author:jaosn
     * @desc:短信发送
     *
     * */
    protected static $instance;

    protected  $sms;
    protected $signname;//签名
    protected $templatecode;//模板
    protected $outid;
    protected $accesskeyid;
    protected $accesskeysecret;
    protected $domain;



    public function __construct() {
        $this->sms = new SignatureHelper();
        $this->signname = SmsConst::SIGNNAME;
        $this->outid = rand(10000,99999);
        $this->accesskeyid = SmsConst::ACCESSKEYID;
        $this->accesskeysecret = SmsConst::ACCESSKEYSECRET;
        $this->domain = SmsConst::SENDMESSAGEURL;
    }

    public static function getInstance($options = array()) {
        if(self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function request($phone,$num) {
        $params = $this->getFileds($phone,$num);
        $result = $this->sms->request($this->accesskeyid,$this->accesskeysecret,$this->domain,array_merge($params,array("RegionId" => "cn-hangzhou", "Action" => "SendSms", "Version" => "2017-05-25",)));
        $result = \helper::object2array($result);
        $TemplateParam = !is_null($params['TemplateParam'])?json_decode($params['TemplateParam'],true) :'';
        $result['outId'] = $TemplateParam['code'];
        return $result;
    }

    public function getFileds($phone,$num) {
        return $params = [
            'PhoneNumbers'=>$phone,
            'SignName'    =>$this->signname,
            'TemplateCode'=>SmsConst::getMessageTemplateCodeItem($num),
            'TemplateParam'=> json_encode(array("code" => rand(10000,99999)),JSON_UNESCAPED_UNICODE),
            'OutId'        => $this->outid,
        ];
    }

   public  static function sendSm($phone,$num) {
        $params = array ();
        // *** 需用户填写部分 ***
        $params["PhoneNumbers"] =$phone;
        $params["SignName"] = SmsConst::SIGNNAME;
       $params["TemplateCode"] = SmsConst::getMessageTemplateCodeItem($num);
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
           SmsConst::ACCESSKEYID,
           SmsConst::ACCESSKEYSECRET,
           SmsConst::SENDMESSAGEURL,
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




