<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 9:33
 */
namespace App\Api\Merchant\V1\Bls;


use App\Api\Merchant\V1\Bls\Model\RechargeModel;

class RechargeBls {
    /**
     * @author jason
     * @desc 商户充值流水
     * */
    public function getRechangeByList($searFormData = [],$orderby = 'created_at',$pageSize = '20'){
        if($searFormData){
            $model = RechargeModel::query();
            foreach ($searFormData as $key=>$val){
                if(!empty($val)){
                    $model->where($key,$val);
                }
            }
            return $model->orderBy($orderby,'desc')->paginate($pageSize);
        }
    }
}