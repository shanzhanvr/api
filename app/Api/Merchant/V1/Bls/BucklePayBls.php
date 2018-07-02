<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/28
 * Time: 18:51
 */
namespace App\Api\Merchant\V1\Bls;

use App\Api\Merchant\V1\Bls\Model\AccountModel;
use App\Api\Merchant\V1\Bls\Model\MerchantModel;
use App\Api\Merchant\V1\Bls\Model\MsCodeModel;
use App\Api\Merchant\V1\Bls\Model\MsPlugModel;
use App\Api\Merchant\V1\Bls\Model\MvCodeModel;
use App\Api\Merchant\V1\Bls\Model\MvPlugModel;
use App\Api\Merchant\V1\Bls\Model\TradeRecodeModel;
use App\Api\Panorama\V1\Bls\Model\PanoConfigModel;
use App\Api\Panorama\V1\Bls\Model\ScenesConfigModel;
use Illuminate\Support\Facades\DB;
use library\Service\Contst\Common\StatusConst;
use library\Service\Contst\Common\TradeTypeConst;

class BucklePayBls {

    //全景功能
    public function bucklePanorama(MerchantModel $merchant,$data = [],PanoConfigModel $panoram){

        try{
            DB::beginTransaction();
            //写扣币记录
            $traderecode = new TradeRecodeModel();
            $traderecode->merchantId = $merchant->id;
            $traderecode->accountId  = $merchant->account->id;
            $traderecode->recodeType = TradeTypeConst::OBJECT_ACTION_TYPE_VIEW;
            $traderecode->recodeSerialNo = \helper::getOrderno();
            $traderecode->outTradeNo = \helper::getOrderno();
            $traderecode->preBlance = $merchant->account->blance;
            $traderecode->blance = $merchant->account->blance - $panoram->amount * $data['term'];
            $traderecode->preAmount = $merchant->account->amount;
            $traderecode->amount = $merchant->account->amount - $panoram->amount *  $data['term'];
            $traderecode->tradeaMount = StatusConst::TRADE_SUCCESS;
            $traderecode->respCode = '0000';
            $traderecode->respMsg = 'success';
            $traderecode->payTime = date('Y-m-d H:i:s');
            $traderecode->ip = \helper::getClientIp();
            $traderecode->save();
            //写商户付费插件记录
            $bucklepay = new MvCodeModel();
            $bucklepay->mId = $merchant->id;
            $bucklepay->plugId = $panoram->funid;
            $bucklepay->plugName = $panoram->feaname;
            $bucklepay->price = $panoram->amount * $data['term'] ;
            $bucklepay->priceType = $data['pricetype'];
            $bucklepay->term =$data['term'];
            $bucklepay->ip = \helper::getClientIp();
            $bucklepay->save();
            //绑定商户全景表
            $msplugn = new MvPlugModel();
            $msplugn->mId = $merchant->id;
            $msplugn->plugId =$panoram->funid;
            $msplugn->plugName =$panoram->feaname;
            $msplugn->useTime = date('Y-m-d H:i:s');
            $msplugn->useETime = \helper::getNextTime($data['pricetype'],$data['term']);
            $msplugn->ip = \helper::getClientIp();
            $msplugn->save();
            $account = AccountModel::find($merchant->account->id);
            $account->blance = $account->blance - $panoram->amount * $data['term'];
            $account->amount = $account->amount - $panoram->amount * $data['term'];
            $account->save();
            DB::commit();
            return true;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }

    public function buckleSence(MerchantModel $merchant,$data = [],ScenesConfigModel $panoram){

        try{
            DB::beginTransaction();
            //写扣币记录
            $traderecode = new TradeRecodeModel();
            $traderecode->merchantId = $merchant->id;
            $traderecode->accountId  = $merchant->account->id;
            $traderecode->recodeType = TradeTypeConst::OBJECT_ACTION_TYPE_RIVER;
            $traderecode->recodeSerialNo = \helper::getOrderno();
            $traderecode->outTradeNo = \helper::getOrderno();
            $traderecode->preBlance = $merchant->account->blance;
            $traderecode->blance = $merchant->account->blance - $panoram->amount * $data['term'];
            $traderecode->preAmount = $merchant->account->amount;
            $traderecode->amount = $merchant->account->amount - $panoram->amount *  $data['term'];
            $traderecode->tradeaMount = StatusConst::TRADE_SUCCESS;
            $traderecode->respCode = '0000';
            $traderecode->respMsg = 'success';
            $traderecode->payTime = date('Y-m-d H:i:s');
            $traderecode->ip = \helper::getClientIp();
            $traderecode->save();
            //写商户付费插件记录
            $bucklepay = new MsCodeModel();
            $bucklepay->mId = $merchant->id;
            $bucklepay->plugId = $panoram->funid;
            $bucklepay->plugName = $panoram->feaname;
            $bucklepay->price = $panoram->amount * $data['term'] ;
            $bucklepay->priceType = $data['pricetype'];
            $bucklepay->term =$data['term'];
            $bucklepay->ip = \helper::getClientIp();
            $bucklepay->save();
            //绑定商户全景表
            $msplugn = new MsPlugModel();
            $msplugn->mId = $merchant->id;
            $msplugn->plugId =$panoram->funid;
            $msplugn->plugName =$panoram->feaname;
            $msplugn->useTime = date('Y-m-d H:i:s');
            $msplugn->useETime = \helper::getNextTime($data['pricetype'],$data['term']);
            $msplugn->ip = \helper::getClientIp();
            $msplugn->save();
            $account = AccountModel::find($merchant->account->id);
            $account->blance = $account->blance - $panoram->amount * $data['term'];
            $account->amount = $account->amount - $panoram->amount * $data['term'];
            $account->save();
            DB::commit();
            return true;
        }catch (\Exception $exception){
            DB::rollBack();
            return false;
        }
    }
}