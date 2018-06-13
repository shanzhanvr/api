<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/5/25
 * Time: 13:23
 */
namespace library\Service\Contst\Pay;
class PayConst{

    //微信扫码
    const wechatnative = 1;
    //微信公众号支付
    const wechatjsapi = 2;
    //微信APP
    const wechatapp = 3;
    //wechath5
    const wechath5 = 4;
    //支付宝扫码
    const alipay = 5;
    //支付宝手机网站
    const alipaywap = 6;
    //支付宝APP
    const alipayapp = 7;

    //微信扫码
    const wechatnative_type = 'services.type.wechatnative';
    //微信公众号支付
    const wechatjsapi_type = 'services.type.wechatjsapi';
    //微信APP
    const wechatapp_type = 'services.type.wechatapp';
    //wechath5
    const wechath5_type = 'services.type.wechath5';
    //支付宝扫码
    const alipay_type = 'services.type.alipay';
    //支付宝手机网站
    const alipaywap_type = 'services.type.alipaywap';
    //支付宝APP
    const alipayapp_type = 'services.type.alipayapp';

    public static function getPayService(){
        return [
            self::wechatnative=>self::wechatnative_type,
            self::wechatjsapi=>self::wechatjsapi_type,
            self::wechatapp=>self::wechatapp_type,
            self::wechath5=>self::wechath5_type,
            self::alipay=>self::alipay_type,
            self::alipaywap=>self::alipaywap_type,
            self::alipayapp=>self::alipayapp_type,
        ];
    }
    public static function getPayServiceByServiceId($serviceId){
        return array_get(self::getPayService(),$serviceId);
    }
}