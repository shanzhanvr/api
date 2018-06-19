<?php

/**
 * TMMemCacheMgr
 * memcache管理类
 * @author jason
 */
namespace library\Service\Cache;
class TMMemCacheMgr implements TMCacheInterface{

    /**
     * 随机缓存基数值
     * @var int
     */
    const CACHE_ZIGZAG_BASE = 30;

    /**
     * 随机缓存最大倍数值
     * @var int
     */
    const CACHE_ZIGZAG_MULTIPLE = 5;

    const DATA_EXPIRE_DIFF = 3600;

    /**
     * memcache对象
     * @var Memcache
     */
    private $cache;


    private static $instance;


    protected static $instanceMap = array();


    protected static $instanceZKMap = array();


    const MEMCACHE_ALIAS_LOCAL_DEFAULT = "local_default";

    /**
     * 是否开启
     * @var boolean
     */
    private $isEnable;

    /**
     * 是否持久连接
     * @var boolean
     */
    private $isPersistent;

    /**
     * 缓存类别、时间映射数组
     * @var array
     */
    protected static $_cacheCategories = array(
        'default'    => 30,
        'config'    => 3600
    );


    /**
     * 根据缓存类别获取缓存时间
     * @param string $category 缓存类别
     * @return int
     */
    protected static function cacheTimeout($category)
    {
        $categories = self::$_cacheCategories;
        if (empty($category) || !isset($categories[$category])){
            $category = 'default';
        }
        return $categories[$category];
    }

    /**
     * 构造函数
     * @param array 配置数组信息，这个配置信息是从getInstance方法传进来的
     * @return void 没有返回值
     */
    protected function __construct() {
        $isEnable = env('MEMCACHED_ISENABLED','true');
        $server['host'] = env('MEMCACHED_HOST','127.0.0.1');
        $server['port'] = env('MEMCACHED_PORT','11211');
        $this->initialize($isEnable,$server);
    }

    /**
     * 得到一个memcache实例
     * @param array $options
     *      options["name"],options["enable"],options["persistent"]
     *      options['server']=array(array('host'=>$server->ip,'port'=>$server->port))
     * @return TMMemCacheMgr
     */
    public static function getInstance($options = array()) {
        $name = self::MEMCACHE_ALIAS_LOCAL_DEFAULT;
        if(isset($options["name"]))
        {
            $name = $options["name"];
        }
        if(empty(self::$instanceMap[$name])) {
            $class = __CLASS__;
            self::$instanceMap[$name] = new $class($options);
        }
        return self::$instanceMap[$name];
    }

    /**
     * 初始化函数
     *
     * @param boolean $isEnable 是否打开
     * @param boolean $isPersistent 是否持久连接
     * @param array $configServers memcache配置数组
     */
    protected function initialize($isEnable,$server) {
        $this->isEnable = $isEnable;
        if ($isEnable) {
            $this->cache = new \Memcached();
            $host = $server["host"];
            $port = empty($server["port"]) ? 11211 : (int) $server["port"];
            $this->cache->addServer($host, $port);
        }
    }

    /**
     * 析构函数，用于关闭所有memcache服务器连接
     *
     * @access public
     */
    public function __destruct() {
        if ($this->isEnable)  {
            $persistent = $this->isPersistent;

            if (!$persistent)  {
                $this->cache->close();
            }
        }
    }
    /**
     * 增加缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @param mix $value 缓存值，只支持字符串
     * @param int $expire 缓存时间，0为不过期，单位为秒
     * @return boolean
     */
    public function add($key, $value, $expire = 0) {
        if (!$this->cache) {
            return false;
        }
        return $this->cache->add($key, $value, 0, $expire);
    }

    /**
     * 递增给定缓存名字的值，原子操作
     *
     * @param string $key 缓存名字
     * @param int $offset 每次递增的值
     */
    public function increment($key, $offset=1) {
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->increment($key, $offset);
    }

    /**
     * 设置缓存对应关系
     *
     * @access public
     * @param string $key 缓存名字
     * @param mixed $value 缓存值
     * @param int $expire 缓存时间，0为不过期，单位为秒
     * @return void
     */
    public function set($key, $value, $expire = 0) {
        if (!$this->cache) {
            return ;
        }
        $this->cache->set($key, $value, 0, $expire);
    }

    /**
     * 设置持久缓存对应关系
     *
     * @access public
     * @param string $key 缓存名字
     * @param boolean $alive is alive
     * @param int $expire 缓存时间，0为不过期，单位为秒
     * @return void
     */
    public function setAlive($key, $alive, $expire) {
        if (!$this->cache)
        {
            return ;
        }
        $this->cache->set("__ALIVE__" . $key, $alive, 0, $expire);
    }

    /**
     * 获取缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @return mixed 缓存值
     */
    public function get($key) {
        if (!$this->cache)
        {
            return null;
        }
        return $this->cache->get($key);
    }

    /**
     * 获取持久缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @return mixed 缓存值
     */
    public function getAlive($key) {
        if (!$this->cache)
        {
            return null;
        }
        return $this->cache->get("__ALIVE__" . $key);
    }

    /**
     * 清除缓存值
     *
     * @access public
     * @param string $key 缓存名字
     * @param boolean $alive 是否是持久的
     * @return void
     */
    public function clear($key, $alive=true) {
        if (!$this->cache)
        {
            return false;
        }
        $delete_result = $this->cache->delete($key);
        if ($alive)
        {
            return $this->clearAlive($key);
        } else {
            return $delete_result;
        }
    }

    /**
     * 清除持久缓存值
     *
     * @access public
     * @param  string $key 缓存名字
     * @return void
     */
    public function clearAlive($key) {
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->delete("__ALIVE__" . $key);
    }


    /**
     * memcache服务器的缓存状态
     *
     * @access public
     */
    public function stat() {
        if ($this->isEnable)
        {
            $extendStats = $this->cache->getExtendedStats();
        }
        else
        {
            $extendStats = array();
        }

        print_r($extendStats);
    }

    /**
     * 递减给定缓存名字的值，原子操作
     *
     * @param string $key 缓存名字
     * @param int $offset 每次递减的值
     */
    public function decrement($key, $offset=1) {
        if (!$this->cache)
        {
            return false;
        }
        return $this->cache->decrement($key, $offset);
    }
}
?>
