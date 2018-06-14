<?php
 /**
 * 缓存接口类，用于统一缓存实现的接口
 *
 * @package sdk.src.framework.cache
 * @author  Salon Zhao <salonzhao@tencent.com>
 * @version $Id: TMCacheInterface.class.php 2111 2012-11-02 08:07:15Z ianzhang $
 */
namespace library\Service\Cache;
interface TMCacheInterface{
    /**
     * 设置缓存对应关系
     * @access public
     * @param string $key    缓存名字
     * @param string $value  缓存值
     * @param string $expire 过期时间
     * @return void
     */
    public function set($key,$value,$expire=0);

    /**
     * 设置持久缓存对应关系
     * @access public
     * @param string $key    缓存名字
     * @param string $alive  缓存值
     * @param string $expire 过期时间，一般默认为0（不过期）
     * @return void
     */
    public function setAlive($key, $alive, $expire);

    /**
     * 获取缓存值
     * @access public
     * @param string $key   缓存名字
     * @return string       缓存值
     */
    public function get($key);

    /**
     * 获取持久缓存值
     * @access public
     * @param string $key   缓存名字
     * @return string       缓存值
     */
    public function getAlive($key);

    /**
     * 清除持久缓存值
     * @param string $key    缓存名字
     * @return void
     */
    public function clearAlive($key);

}
