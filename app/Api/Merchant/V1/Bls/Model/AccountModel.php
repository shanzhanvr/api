<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 9:49
 */

namespace App\Api\Merchant\V1\Bls\Model;


class AccountModel extends BaseModel {
    protected $table= 'account';  //指定表名

    protected $fillable =['merchantId','blance','amount','ip','status'];

}