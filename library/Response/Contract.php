<?php
namespace library\Response;

/**
 * Interface Contract
 * @package Dffl\Service\Library\Response
 */
interface Contract
{
    /**
     * @param array $data
     * @return mixed
     */
    public static function success($data = []);

    /**
     * @param $code
     * @param string $msg
     * @return mixed
     */
    public static function error($code, $msg = '');

    /**
     * @param $callback
     * @return mixed
     */
    public static function jsonp($callback);
}