<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/30
 * Time: 20:14
 */
namespace  App\api\Pay\V1\Controllers\Flicker;
use App\Api\BaseController;
use App\Api\Common\V1\Bls\Model\ScenelCollFrees\ScenelCollectModel;
use App\Api\Merchant\V1\Bls\Model\AccountModel;
use App\Api\Merchant\V1\Bls\Model\SceneCollFressModel;
use App\Api\Merchant\V1\Bls\Model\ScenelCollFreesRcordModel;
use App\Api\Merchant\V1\Bls\Model\TradeRecodeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use library\Response\JsonResponse;
use library\Service\Cache\TMRedisCacheMgr;
use JWTAuth;
use library\Service\Contst\Common\RechangeConst;
use library\Service\Contst\Common\TradeTypeConst;
use library\Service\Log\BLog;

class FlickerController extends BaseController{

    public $merchant=null;
    public function __construct() {
        $this->merchant = JWTAuth::parseToken()->authenticate();
    }

    /**
     * @author:jason
     * @desc:判断当前是否收费
     * @param secenlId
     * */
    public function checkIsPay(){
        $input = Input::all();
        if(empty($this->merchant)){
            return JsonResponse::error('0','非法操作');
        }
        if(!isset($input['secenlId']) || empty($input['secenlId'])){
            return JsonResponse::error(0,'参数错误');
        }
        //判断当前该场景是否收费
        $scenelColl = ScenelCollectModel::find($input['secenlId']);
        if(empty($scenelColl)){
            return JsonResponse::error(0,'该功能已关闭或不存在');
        }
        return JsonResponse::success(['isPay'=>$scenelColl->isPay,'price'=>$scenelColl->price]);
    }

    /**
     * @author:jason
     * @desc:摄影全景功能
     * @params：secenlCollId 全景功能ID num：购买时长
     *
     * */
    public function scenelCollPay(){
        $input = Input::all();
        $lock = TMRedisCacheMgr::getInstance();

        if(empty($this->merchant) || empty($this->merchant->mobile)){
            JsonResponse::error(0,'非法操作');
        }
        if(!$lock->setLock($this->merchant->mobile)){
            return JsonResponse::error(0,'请勿频繁访问');
        }
        if(!isset($input['paydata']) && empty($input['paydata'])){
            return JsonResponse::error(0,'参数错误');
        }
        $paydata = json_decode($input['paydata'],true);
        $blog = BLog::get_instance();
        //判断当前该账户余额是否充足
        $payAmount = 0;
        foreach ($paydata as $k=>$v){
            $scenelColl = ScenelCollectModel::find(12);
            $payAmount = $this->getSumamount($payAmount,$this->getBcmulAmount($scenelColl->price,$v));
        }
        if(empty($this->merchant->account->blance) || $this->compareAmount($this->merchant->account->blance,$payAmount) == '-1'){
            return JsonResponse::error(0,'账户余额不足！请充值');
        }
        DB::beginTransaction();
        foreach ($paydata as $k=>$item) {
            try {
                $input['num'] = $item;
                $input['secenlCollId'] = $k;
                $scenelColl = ScenelCollectModel::find($k);
                $account = AccountModel::query()->where('merchantId', $this->merchant->id)->first();
                $scenelColl->price = $scenelColl->price * $input['num'];
                //记录摄影购买功能记录
                $orderNo = \helper::getOrderno();
                $scenelCollRecode = new ScenelCollFreesRcordModel();
                $scenelCollRecode->merchantId = $this->merchant->id;
                $scenelCollRecode->scenceCollId = $input['secenlCollId'];
                $scenelCollRecode->startTime = date('Y-m-d H:i:s');
                $scenelCollRecode->endTime = \helper::getNextTime($scenelColl->isPay, $input['num']);
                $scenelCollRecode->ip = \helper::getClientIp();
                $scenelCollRecode->recodeSerialNo = $orderNo;
                $scenelCollRecode->price = $scenelColl->price;
                $scenelCollRecode->save();
                $collfree = SceneCollFressModel::query()->where('merchantId', $this->merchant->id)->where('scenceCollId', $scenelColl->id)->first();
                if (empty($collfree)) {
                    $collfree = new SceneCollFressModel();
                }
                $collfree->price = $scenelColl->price;
                $collfree->merchantId = $this->merchant->id;
                $collfree->scenceCollId = $input['secenlCollId'];
                $collfree->startTime = date('Y-m-d H:i:s');
                $collfree->endTime = \helper::getNextTime($scenelColl->isPay, $input['num'], !empty($collfree->endTime) ? $collfree->endTime : '');
                $collfree->ip = \helper::getClientIp();
                $collfree->save();
                //写扣币记录
                $tradeRecode = new TradeRecodeModel();
                $tradeRecode->merchantId = $this->merchant->id;
                $tradeRecode->accountId = $this->merchant->account->id;
                $tradeRecode->recodeSerialNo = $orderNo;
                $tradeRecode->outTradeNo = \helper::getOrderno();
                $tradeRecode->recodeType = TradeTypeConst::OBJECT_ACTION_TYPE_VIEW;
                $blance = $this->merchant->account->blance;//总金额
                $amount = $this->merchant->account->amount;
                $tradeRecode->preBlance = $this->merchant->account->blance;
                $tradeRecode->blance = $this->merchant->account->blance - $scenelColl->price;
                $tradeRecode->preAmount = !empty($this->merchant->account->amount) ? $this->merchant->account->amount : 0;
                if ($this->compareAmount($this->getSubAmount($blance, $amount), $scenelColl->price) != '-1') { //  -1 说明充值金额不足   0相等 1:大于充值金额充足先从充值金额里面扣 如果充值金额和提现金额相等
                    $tradeRecode->amount = !empty($this->merchant->account->amount) ? $this->merchant->account->amount : 0;
                } else {
                    //计算充值金额
                    $rechangeAmount = $this->getSubAmount($blance, $amount);//总金额减去可提现金额
                    $tradeRecode->amount = $this->getSubAmount($amount, $this->getSubAmount($rechangeAmount, $scenelColl->price));
                }
                $tradeRecode->tradeaMount = $scenelColl->price;
                $tradeRecode->status = RechangeConst::RECHANGE_ACTION_STATUCT_SUCCESS;
                $tradeRecode->respCode = '0000';
                $tradeRecode->respMsg = 'success';
                $tradeRecode->payTime = date('Y-m-d H:i:s');
                $tradeRecode->ip = \helper::getClientIp();
                $tradeRecode->save();
                //更新账户变动金额
                $account->blance = $tradeRecode->blance;
                $account->amount = $tradeRecode->amount;
                $account->save();
                DB::commit();
                return JsonResponse::success();
            } catch (\Exception $e) {
                DB::rollBack();
                return JsonResponse::error(0, '支付失败');
            }
        }
    }
}