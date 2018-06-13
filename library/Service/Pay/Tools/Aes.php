<?php
namespace library\Service\Pay\Tools;
class Aes{
    /**
     * [opensslDecrypt description]
     * 使用openssl库进行加密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function opensslEncrypt($sStr, $sKey, $method = 'AES-128-ECB'){
        $str = openssl_encrypt($sStr,$method,$sKey);
        return $str;
    }
    /**
     * [opensslDecrypt description]
     * 使用openssl库进行解密
     * @param  [type] $sStr
     * @param  [type] $sKey
     * @return [type]
     */
    public static function opensslDecrypt($sStr, $sKey, $method = 'AES-128-ECB'){
        $str = openssl_decrypt($sStr,$method,$sKey);
        return $str;
    }
}