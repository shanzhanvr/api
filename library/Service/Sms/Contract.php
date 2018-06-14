<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/14
 * Time: 9:07
 */
namespace  library\Service\Sms;

interface Contract {

    public function request($phone,$num);

    public function getFileds($phone,$num);



}