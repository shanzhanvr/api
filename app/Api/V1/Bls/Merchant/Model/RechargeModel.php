<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 9:40
 */

namespace App\Api\V1\Bls\Merchant\Model;

class RechargeModel extends BaseModel{

    protected $table= 'recharge';  //指定表名

    protected $fillable=['merchantId','rechargeType','rechargeSerialNo','amount','outTradeNo','status','ip'];


}