<?php
/**
 * Created by PhpStorm.
 * User: Abduahad
 * Date: 2018/3/22
 * Time: 10:32
 */
namespace library\Service\Pay\Tools;
class Rsa {
    public static $publicKey;
    public static $privateKey;
    private static $privateKeyFile;
    public static $xtbPriKey;

    public function __construct() {
        self::$privateKey = file_get_contents(realpath(str_replace('public','',getcwd()).'/library' ).'/cert/xuetongbao/merchant_private_key.pem');
        self::$privateKeyFile = openssl_get_privatekey(self::$privateKey);
        self::$publicKey = file_get_contents(realpath(str_replace('public','',getcwd()).'/library' ).'/cert/xuetongbao/merchant_public_key.pem');
        self::$xtbPriKey = file_get_contents(realpath(str_replace('public','',getcwd()).'/library' ).'/cert/xuetongbao/xtb_key.pem');
    }
    /*
     * 私钥加密
     */
    public static function publicEncrypt($data, $publicKey) {
        $encode_data = '';
        $split = str_split($data, 100);// 1024bit && OPENSSL_PKCS1_PADDING  不大于117即可
        foreach ($split as $part) {
            $isOkay = openssl_public_encrypt($part, $en_data, $publicKey);
            if(!$isOkay){
                return false;
            }
            // echo strlen($en_data),'<br/>';
            $encode_data .= base64_encode($en_data);
        }
        return $encode_data;
    }

    /*
     * 公钥解密
     */
    public static function publicDecrypt($data, $publicKey){
        $decode_data = '';
        $spList = str_split($data,172);
        foreach ($spList as $part) {
            $isOkay = openssl_public_decrypt(base64_decode($part), $decryted, $publicKey);
           if(!$isOkay){
                return false;
            }
            $decode_data .= $decryted;
        }
        return $decode_data;
    }

    /*
     * 私钥解密
     */
    public static function privateDecrypt($data, $privateKey) {
        $decode_data = '';
        $spList = str_split($data,172);
        foreach ($spList as $part) {
            $isOkay = openssl_private_decrypt(base64_decode($part), $decrypted, $privateKey);
            if(!$isOkay){
                return false;
            }
            $decode_data .= $decrypted;
        }
        return $decode_data;
    }
}