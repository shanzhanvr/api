<?php
/**
 *---------------------------------------------------------------------------
 *
 *                  T E N C E N T   P R O P R I E T A R Y
 *
 *     COPYRIGHT (c)  2008 BY  TENCENT  CORPORATION.  ALL RIGHTS
 *     RESERVED.   NO  PART  OF THIS PROGRAM  OR  PUBLICATION  MAY
 *     BE  REPRODUCED,   TRANSMITTED,   TRANSCRIBED,   STORED  IN  A
 *     RETRIEVAL SYSTEM, OR TRANSLATED INTO ANY LANGUAGE OR COMPUTER
 *     LANGUAGE IN ANY FORM OR BY ANY MEANS, ELECTRONIC, MECHANICAL,
 *     MAGNETIC,  OPTICAL,  CHEMICAL, MANUAL, OR OTHERWISE,  WITHOUT
 *     THE PRIOR WRITTEN PERMISSION OF :
 *
 *                        TENCENT  CORPORATION
 *
 *       Advertising Platform R&D Team, Advertising Platform & Products
 *       Tencent Ltd.
 *---------------------------------------------------------------------------
 */

/**
 * phpRedis客户端工厂
 *
 * @package sdk.src.framework.redis
 * @author  ianzhang <jasonmark@yeah.net>
 * @version $Id: TMPHPRedisClientFactory.class.php 2101 2012-11-01 10:04:01Z ianzhang $
 */
namespace library\Service\Redis;

use Illuminate\Support\Facades\Redis;

class TMPHPRedisClientFactory extends TMRedisClientAbstractFactory {

    /**
     *
     * 客户端连接数组
     * @var array
     */
    protected static $clientArray = array();

    /**
     *
     * 是否注册了shutdown方法
     * @var boolean
     */
    protected static $hasRegisterShutDown = false;

    /**
     * 根据名字获取客户端
     * @param string $name 连接的名字
     * @throws TMConfigException
     */
    public static function getClient($name = "phpredis") {
        $host = env("REDIS_HOST",'127.0.0.1');
        $port = env("REDIS_PORT",'6379');
        $timeout = env("REDIS_TIMEOUT",0);
        return self::getClientForDetails($host, $port, $timeout);
    }


    /**
     * 根据各个参数获取一个redis客户端
     * @param string $host ip
     * @param int $port 端口
     * @param int $timeout 超时
     * @param string $auth 校验密码
     * @param string $name 连接的别名
     */
    public static function  getClientForDetails($host, $port, $timeout = 0, $auth = "", $name = "") {
        if(empty($name)) {
            $name = $host."_".$port;
        }
        if(!isset(self::$clientArray[$name])){
            $redis = new Redis();
            $redis->connect($host, $port, $timeout);
            if(!empty($auth)) {
                $redis->auth($auth);
            }
            self::$clientArray[$name] = $redis;
        }
        else{
            $redis = self::$clientArray[$name];
            try{
                $redis->ping();
            }catch(\RedisException $re) {
                $redis->connect($host, $port, $timeout);
            }
        }
        return $redis;
    }

    /**
     * 关闭所有客户端
     */
    public static function closeAllClient() {
        foreach (self::$clientArray as $name => $client) {
            $client->close();
            unset(self::$clientArray[$name]);
        }
    }
    /**
     * 关闭单个客户端
     * @param string $name 连接的别名
     */
    public static function closeClient($name = "phpredis") {
        if(isset(self::$clientArray[$name]))  {
            self::$clientArray[$name]->close();
            unset(self::$clientArray[$name]);
        }
    }
}
