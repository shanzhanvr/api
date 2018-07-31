<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/5/22
 * Time: 21:00
 */

/**
 * 是否为手机号码
 * @param $string
 * @return bool
 */
use Illuminate\Contracts\View\Factory as ViewFactory;
class helper {

    /**
     * @var array    用于生成随机兑换码
     * @access private
     */
    private static $charSet = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y',
        'a','b','c','d','e','f','g','h','j','k','m','n','p','q','r','s','t','u','v','w','x','y');

    public static function isMobile($string) {
        return !!preg_match('/^1[3|4|5|7|8|9]\d{9}$/', $string);
    }
    public static function checkPassword($password){
        return !!preg_match('/^(\w){6,20}$/',$password);
    }

    public static function setLock($key,$v){

    }
    public static function getRandomString($length,$prefix="",$isLowerCase=false) {
        $returnString = $prefix;
        for($i = 0; $i < $length; $i ++)
        {
            $arrayIndex = self::getCharLength($isLowerCase);
            $randASC = self::$charSet[mt_rand(0,$arrayIndex)];
            $returnString .=$randASC;
        }
        return $returnString;
    }

    /**
     * 用于生成随机兑换码的取长度函数
     * @param boolean $isLowerCase 是否允许出现小写字母
     */
    private static function getCharLength($isLowerCase) {
        if($isLowerCase){
            $arrayIndex = count(self::$charSet);
        }else{
            $arrayIndex = 0;
            foreach (self::$charSet as $char) {
                if (($char >= '0' && $char <= '9') || ($char >= 'A' && $char <= 'Z')) {
                    $arrayIndex++;
                }
            }
        }
        $arrayIndex--;
        return $arrayIndex;
    }

    public static function object2array(&$object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }

    /*
      * 随机生成订单号
      *
      * */
    public static function getOrderno(){
        list($usec, $sec) = explode(" ", microtime());
        $usec = substr(str_replace('0.', '', $usec), 0 ,5);
        $orderno = date('Ymd').$usec.rand(10,99999);
        return $orderno;
    }

    /*
     * 获取IP地址
     *
     * */
    public static function getClientIp(){
        if (isset ( $_SERVER ['HTTP_CLIENT_IP'] ) and ! empty ( $_SERVER ['HTTP_CLIENT_IP'] ))
        {
            return self::filterIp ( $_SERVER ['HTTP_CLIENT_IP'] );
        }
        if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ) and ! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] ))
        {
            $ip = strtok ( $_SERVER ['HTTP_X_FORWARDED_FOR'], ',' );
            do
            {
                $ip = ip2long ( $ip );

                //-------------------
                // skip private ip ranges
                //-------------------
                // 10.0.0.0 - 10.255.255.255
                // 172.16.0.0 - 172.31.255.255
                // 192.168.0.0 - 192.168.255.255
                // 127.0.0.1, 255.255.255.255, 0.0.0.0
                //-------------------
                if (! (($ip == 0) or ($ip == 0xFFFFFFFF) or ($ip == 0x7F000001) or (($ip >= 0x0A000000) and ($ip <= 0x0AFFFFFF)) or
                    (($ip >= 0xC0A8FFFF) and ($ip <= 0xC0A80000)) or (($ip >= 0xAC1FFFFF) and ($ip <= 0xAC100000))))
                {
                    return long2ip ( $ip );
                }
            }
            while ( $ip = strtok ( ',' ) );
        }
        if (isset ( $_SERVER ['HTTP_PROXY_USER'] ) and ! empty ( $_SERVER ['HTTP_PROXY_USER'] ))
        {
            return self::filterIp ( $_SERVER ['HTTP_PROXY_USER'] );
        }
        if (isset ( $_SERVER ['REMOTE_ADDR'] ) and ! empty ( $_SERVER ['REMOTE_ADDR'] ))
        {
            return self::filterIp ( $_SERVER ['REMOTE_ADDR'] );
        }
        else
        {
            return "0.0.0.0";
        }
    }

    public static function filterIp($key) {
        $key = preg_replace("/[^0-9.]/", "", $key);
        return preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $key) ? $key : "0.0.0.0";
    }
    /**
     * 产生随机字符串，不长于32位
     * @param $length —— 字符串的长度
     * @return string
     * @author jason
     */
    public static function createNoncestr($length = 32){
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = "";
        for ($i=0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = []){
        $factory = app(ViewFactory::class);
        if (func_num_args() === 0) {
            return $factory;
        }
        return $factory->make($view, $data, $mergeData);
    }
    /*
     * $type : 1 年 2 月
     * $num 值
     * */
    public static function getNextTime($type = 1,$num = 1,$date=''){
        $nowtime = !empty($date) ? $date : date('Y-m-d H:i:s');
        if($type == 1){
            return date("Y-m-d H:i:s",strtotime("+".$num."years",strtotime($nowtime)));
        }elseif($type == 2){
           return date("Y-m-d H:i:s",strtotime("+".$num."months",strtotime($nowtime)));
        }
    }
}