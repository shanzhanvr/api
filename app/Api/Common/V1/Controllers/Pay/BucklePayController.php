<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/27
 * Time: 18:30
 */
namespace App\Api\Common\V1\Controllers\Pay;

use App\Api\Controllers\BaseController;
use Illuminate\Support\Facades\Input;
use library\Service\Contst\Common\TradeTypeConst;

class BucklePayController extends BaseController{

    public function buckle(){
        $objectType = Input::get('objectType');//对象类型id
        $objectId = Input::get('objectId');//要操作的对象的id
        $logtoken = Input::get('logtoken');//商户登录成功之后的一个token值需要从接口获取
        if($objectType == TradeTypeConst::OBJECT_ACTION_TYPE_SCENE){//场景
            //判断对象ID是否存在
        }else if($objectType == TradeTypeConst::OBJECT_ACTION_TYPE_RIVER){//全景
            //
        }
    }
}