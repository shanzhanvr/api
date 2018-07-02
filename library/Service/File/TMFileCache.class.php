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
 *
 * @package sdk.src.framework.cache
 * @author  Samon Ma <wujunjun2015@gmail.com>
 */
namespace library\Service\File;
class TMFile
{
    /**
     * 缓存单例对象变量
     * @var TMFileCache
     */
    private static $instance;
    private $path;//文件绝对路径
    private $name;//文件名称
    private $content;//文件内容

    /**
     * 获取文件缓存单例对象
     * @access public
     * @return TMFileCache
     */
    public static function getInstance()
    {
        if(self::$instance == null)
        {
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * 根据文件路径、文件名、文件内容创建文件
     * @access public
     * @return void
     */
    public function commit($basePath)
    {
        if (!is_dir($basePath)) {
            $oldumask = umask(0);
            mkdir($this->path, 0777, true);
            umask($oldumask);
        }
        file_put_contents($this->path.$this->name, $this->content);
        chmod($this->path.$this->name, 0777);
    }

    /**
     * 设置文件路径、文件名、文件内容
     *
     * @access public
     * @param string $filePath  文件路径
     * @param string $fileName      文件名字
     * @param string $fileContent   文件内容
     * @return void
     */
    public function execute($basePath, $fileName, $fileContent)
    {
        $this->setPath($basePath);
        $this->setName($fileName);
        $this->setContent($fileContent);

        $this->commit($basePath);
    }

    /**
     * 设置文件路径
     *
     * @access protected
     * @param string $path 文件路径
     * @return void
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * 设置文件名字
     *
     * @access protected
     * @param string $name 文件名字
     * @return void
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 设置文件内容
     *
     * @access protected
     * @param string $content 文件内容
     * @return void
     */
    protected function setContent($content)
    {
        $this->content = $content;
    }
}
