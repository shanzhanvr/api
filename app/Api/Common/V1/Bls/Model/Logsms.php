<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/21
 * Time: 19:07
 */
namespace App\Api\Common\V1\Bls\Model;

class Logsms extends BaseModel{

    protected $table = 'logsms';

    protected $fillable = ['smsContent','smsPhoneNumber','smsReturnCode','smsFunc','smsCode','ip'];
}