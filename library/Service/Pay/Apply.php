<?php
/**
 * Created by PhpStorm.
 * User: Abduahad
 * Date: 2018/3/23
 * Time: 14:36
 */
namespace library\Service\Pay;


use library\Service\Contst\Pay\PayConst;
use library\Service\Pay\Tools\Aes;
use library\Service\Pay\Tools\Rsa;

class Apply {

    private static $strKey;
    private static $appId;
    private static $notifyUrl;
    private static $callback;
    private static $unfiledurl;
    public static $publicKey;
    public static $privateKey;
    public static $privateKeyFile;
    public static $xtbPriKey;

    public function __construct(){
        self::$appId= env('LEARNING_PAYAPPID');
        self::$strKey = env('LEARNING_PAYAPP_SERECTKEY');
        self::$notifyUrl = env('LEARNING_PAYAPP_NOTIFY');
        self::$callback =  env('LEARNING_PAYAPPPAY_FRONTJUMPURL');;
        self::$unfiledurl = env('LEARNING_UNIFIED_PAYURL');
        self::$privateKey = file_get_contents(realpath(str_replace('public','',getcwd()).'/library' ).'/cert/xuetongbao/merchant_private_key.pem');
        self::$privateKeyFile = openssl_get_privatekey(self::$privateKey);
        self::$publicKey = file_get_contents(realpath(str_replace('public','',getcwd()).'/library' ).'/cert/xuetongbao/merchant_public_key.pem');
        self::$xtbPriKey = file_get_contents(realpath(str_replace('public','',getcwd()).'/library' ).'/cert/xuetongbao/xtb_key.pem');
    }
    /*
     *聚合支付
     */
    public static function unifiedOrder($sn,$amount,$body,$service,$openId=''){
        $payOptions = array(
            'services'       => PayConst::getPayServiceByServiceId($service),
            'version'        =>  'v1.0',
            'merchantOrderNo'=>  $sn,
            'subject'          => $body,
            'body'          =>  $body,
            'amount'        =>  strval($amount),
            'tranDateTime'  =>  date('YmdHis'),
            'notifyUrl'     =>  self::$notifyUrl,
            'frontJumpUrl'  =>  self::$callback,
            'clientIp'=>\helper::getClientIp(),
            'openId'=>$openId
        );
        return self::doResponse($payOptions);
    }
    /*
     *根据订单号查询
     *
     */
    public static function checkOrder($sn){
        $payOptions = array('merchantOrderNo'=>$sn);
        return self::doResponse($payOptions);
    }
    /**
     * 返回处理之后的加密
     *
     * */
    public static function getFileds($payOptions){
        $payOptionsJson = json_encode($payOptions);
        $aesKey = \helper::createNoncestr(16);
        $connectOptions = [
            'appId'=>self::$appId,
            'encryptData'=>Aes::opensslEncrypt($payOptionsJson,$aesKey),
            'encryptKey'=>Rsa::publicEncrypt($aesKey,self::$xtbPriKey),
            'signData'=>self::getSign(utf8_encode($payOptionsJson),self::$privateKey)
        ];
        return json_encode($connectOptions);
    }
    public static function doResponse($payOptions){
        $fileData = self::getFileds($payOptions);//处理参数
        $response = self::doRequest($fileData);//请求接口
        if(!empty($response)){
            $response = json_decode($response[1]);
            return !empty($response->respCode)&&!empty($response) ? array('code'=>0,'msg'=>$response->respMsg):self::signDecrypt($response);
        }
        return array('code'=>0,'msg'=>'接口请求失败');
    }
    public static function signDecrypt($response){
        $aesKey = Rsa::privateDecrypt($response->encryptKey,self::$privateKey);
        $encData = json_decode(Aes::opensslDecrypt($response->encryptData,$aesKey));
        if(intval($encData->respCode) == 0000){
            return array('code'=>1,'msg'=>$encData->respMsg,'data'=>$encData);
        }
    }
    /**
     * PHP发送Json对象数据
     * @param $url 请求url
     * @param $jsonStr 发送的json字符串
     * @return array
     */
    private static function doRequest($jsonStr) {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, self::$unfiledurl);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($jsonStr)
                )
            );
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return array($httpCode, $response);
        }catch (\Exception $exception){
            return array('code'=>0,'msg'=>'接口访问错误');
        }
    }
    /*
    * 获取签名
    */
    public static function getSign($data) {
        openssl_sign($data,$sign,self::$privateKeyFile);
        openssl_free_key(self::$privateKeyFile);
        $sign=base64_encode($sign);
        return $sign;
    }
}