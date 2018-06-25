<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/5
 * Time: 15:53
 */
namespace App\Api\Merchant\V1\Bls\Model;

class TradeRecodeModel extends BaseModel {


    protected $table= 'recode';  //指定表名

    protected $primaryKey= 'id';    //指定主键

    protected $fillable =['preBlance','blance','preAmount','tradeaMount','status','respCode','respMsg','payTime'];
}