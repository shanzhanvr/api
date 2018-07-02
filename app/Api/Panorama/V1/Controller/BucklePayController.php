<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/27
 * Time: 18:30
 */
namespace App\Api\Panorama\V1\Controller;

use App\Api\BaseController;
use App\Api\Merchant\V1\Bls\BucklePayBls;
use App\Api\Merchant\V1\Bls\Model\MerchantModel;
use App\Api\Panorama\V1\Bls\Model\PanoConfigModel;
use Illuminate\Support\Facades\Input;
use library\Response\JsonResponse;
use library\Service\Contst\Common\TradeTypeConst;
use JWTAuth;
class BucklePayController extends BaseController {

    public function buckle(){
        $objectType = Input::get('object');//对象类型id
        $objectId = Input::get('objectid');//要操作的对象的id
        if($objectType == TradeTypeConst::OBJECT_ACTION_TYPE_SCENE){//场景
            //判断对象ID是否存在
        }else if($objectType == TradeTypeConst::OBJECT_ACTION_TYPE_VIEW){//全景
            $timetype = Input::get('timetype');
            $term = Input::get('term');
            $panoram = PanoConfigModel::query()->where('funid',$objectId)->first();
            if(empty($panoram) || !$panoram->fstatus){
                return JsonResponse::error(0,'场景功能不存在或已关闭');
            }
            $merchant = JWTAuth::parseToken()->authenticate();
            //开通该商户的场景功能 判断商户是否存在
            if(empty($merchant)){
                return JsonResponse::error(0,'该商户不存在');
            }
            //判断该商户账户闪币是否不足
            if($merchant->account->blance < $panoram->paymoney * $term){
                return JsonResponse::error(0,'账户余额不足!请充值');
            }
            $bls = new BucklePayBls();
            $data = ['pricetype'=>$timetype,'term'=>$term];
            if($timetype == 1){
                $panoram->amount = $panoram->paymoney;
            }elseif($timetype == 2){
                $panoram->amount = $panoram->monthmoney;
            }
            $accectId = $bls->bucklePanorama($merchant,$data,$panoram);
            if($accectId){
                return JsonResponse::success();
            }
            return JsonResponse::error(0,'扣币失败!');
        }
    }
}