<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/27
 * Time: 17:55
 */
namespace App\Api\Merchant\V1\Controllers\Pay;

use App\Api\BaseController;
use App\Api\Merchant\V1\Bls\Model\AccountModel;
use App\Api\Merchant\V1\Bls\Model\RechargeModel;
use App\Api\Merchant\V1\Bls\Model\TradeRecodeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use library\Response\JsonResponse;
use library\Service\Cache\TMRedisCacheMgr;
use library\Service\Contst\Common\RechangeConst;
use library\Service\Contst\Common\TradeTypeConst;
use library\Service\Contst\Pay\PayConst;
use library\Service\Log\BLog;
use library\Service\Pay\Apply;
use library\Service\Pay\Tools\Aes;
use library\Service\Pay\Tools\Rsa;
use Validator,JWTAuth;

class RechangePayController extends BaseController {

    protected $body;
    protected $blog;
    public function __construct() {
        parent::__construct();
        $this->body = env('MERCHANT_PAY_BODY');
        $this->blog = BLog::get_instance();
    }
    //下单接口
    public function alipayPay() {
        try {
            $input = Input::all();
            $user = JWTAuth::parseToken()->authenticate();
            $redis = TMRedisCacheMgr::getInstance();
            //调用枷锁服务
            if (!$redis->setLock($user->mobile, 2)) {
                return JsonResponse::error(0, '请勿频繁访问');
            }
            $validator = Validator::make(
                $input, ['amount' => 'required|Numeric', 'rechargeType' => 'required|' . Rule::in(RechangeConst::desc())],
                ['amount.required' => '充值金额不能为空', 'amount.Numeric' => '充值金额为整数', 'rechargeType.required' => '请选择充值方式', 'rechargeType.in' => '请选择充值方式']
            );
            if ($validator->fails()) {
                return JsonResponse::error(0, '验证失败', $validator->errors()->toArray());
            }
            $rechargeType = '';
            if ($input['rechargeType'] == 1) {
                $rechargeType = PayConst::wechatnative;
            } elseif ($input['rechargeType'] == 2) {
                $rechargeType = PayConst::alipay;
            }
            $applyPay = new Apply(env('LEARNING_PAYAPP_MERCHANT_NOTIFY'));
            $this->blog->log('merchant_' . $user->mobile, '-----start---发起的支付----参数' . json_encode(Input::all()));
            $response = $applyPay::unifiedOrder(\helper::getOrderno(), $input['amount'], $this->body, $rechargeType);
            DB::connection('db_vr_merchant')->beginTransaction();
            $this->blog->log('merchant_' . $user->mobile, '-----调用支付接口---发起的支付' . json_encode($response));
            if ($response['code'] && !empty($response['data'])) {
                    $payData = \helper::object2array($response['data']);
                    //产生预支付订单
                    $rechange['merchantId'] = $user->id;
                    $account = AccountModel::query()->where('merchantId', $user->id)->first();
                    $rechange['accountId'] = $account->id;
                    $rechange['amount'] = $this->getYuanFromFen($input['amount']);
                    $rechange['rechargeType'] = $input['rechargeType'];
                    $rechange['rechargeSerialNo'] = $payData['merchantOrderNo'];
                    $rechange['outTradeNo'] = $payData['tradeOrderNo'];
                    $rechange['status'] = RechangeConst::RECHANGE_ACTION_STATUCT_ING;
                    $rechange['ip'] = \helper::getClientIp();
                    $rechange['created_at'] = date('Y-m-d H:i:s');
                    $rechange['updated_at'] = date('Y-m-d H:i:s');
                    $rechargeId = DB::connection('db_vr_merchant')->table('recharge')->insertGetId($rechange);
                    //交易记录
                    $recode['merchantId'] = $user->id;
                    $recode['accountId'] = $account->id;
                    $recode['recodeType'] = TradeTypeConst::OBJECT_ACTION_TYPE_RECHANGE;
                    $recode['recodeSerialNo'] = $payData['merchantOrderNo'];
                    $recode['outTradeNo'] = $payData['tradeOrderNo'];
                    $recode['preBlance'] = $account->blance;
                    $recode['blance'] = (int)($account->blance + $this->getYuanFromFen($input['amount']));
                    $recode['preAmount'] = $account->amount;//变动前可提现金额
                    $recode['amount'] = $account->amount;//变动后可提现金额
                    $recode['tradeaMount'] = $this->getYuanFromFen($input['amount']);
                    $recode['status'] = RechangeConst::RECHANGE_ACTION_STATUCT_ING;
                    $recode['ip'] = \helper::getClientIp();
                    $recode['created_at'] = date('Y-m-d H:i:s');
                    $recode['updated_at'] = date('Y-m-d H:i:s');
                    $recodeId = DB::connection('db_vr_merchant')->table('recode')->insertGetId($recode);
                    if($rechargeId && $recodeId){
                        DB::connection('db_vr_merchant')->commit();
                        return JsonResponse::success(['qrcodeUrl' => $payData['qrcodeUrl'], 'merchantOrderNo' => $payData['merchantOrderNo']]);
                    }
            } else {
                $this->blog->log('merchant_' . $user->mobile, '-----调用支付接口---产生异常' . json_encode($response));
                return JsonResponse::error(0, '网络错误!请稍后重试');
            }
        }catch (\Exception $e){
            DB::connection('db_vr_merchant')->rollBack();
            return JsonResponse::error(0,'网络繁忙!请稍后重试');
        }
    }

    //支付回调接口
    public function receivePay(){
        //开启事物
        DB::connection('db_vr_merchant')->beginTransaction();
        try {
        $responseData = file_get_contents('php://input');
        $this->blog->log('receivepay','支付接口回调解密前的报文:' . $responseData);
            if (!empty($responseData)) {
                $responseData = json_decode($responseData, true);
                if (isset($responseData['appId']) && env('LEARNING_PAYAPPID') == $responseData['appId']) {
                    $applyPay = new Apply(env('LEARNING_PAYAPP_NOTIFY'));
                    $aesKey = Rsa::privateDecrypt($responseData['encryptKey'], $applyPay::$privateKey);
                    $data = Aes::opensslDecrypt($responseData['encryptData'], $aesKey);
                    $this->blog->log('receivepay','支付接口回调解密后的报文:' . $data);
                    $data = json_decode($data, true);
                    if ($data['respCode'] == '0000') {
                        $rechangeModel = RechargeModel::query()->where('outTradeNo', $data['tradeOrderNo'])->where('status', RechangeConst::RECHANGE_ACTION_STATUCT_ING)->first();
                        $tradeModel = TradeRecodeModel::query()->where('outTradeNo', $data['tradeOrderNo'])->where('status', RechangeConst::RECHANGE_ACTION_STATUCT_ING)->first();
                        $status = RechangeConst::RECHANGE_ACTION_STATUCT_SUCCESS;
                        if ($rechangeModel && $tradeModel) {
                            $rechangeData['status'] = $status;
                            $rechangeData['respCode'] = $data['respCode'];
                            $rechangeData['respMsg'] = $data['respMsg'];
                            $rechangeModel['payTime'] = $data['payTime'];
                            $rechangeData['updated_at'] = date('Y-m-d H:i:s');
                            $rechargeId = DB::connection('db_vr_merchant')->table('recharge')->where('outTradeNo', $data['tradeOrderNo'])->update($rechangeData);
                            $tradeData['status'] = $status;
                            $tradeData['respCode'] = $data['respCode'];
                            $tradeData['respMsg'] = $data['respMsg'];
                            $tradeData['payTime'] = $data['payTime'];
                            $tradeData['updated_at'] = date('Y-m-d H:i:s');
                            $recodeId = DB::connection('db_vr_merchant')->table('recode')->where('outTradeNo',$data['tradeOrderNo'])->update($tradeData);
                            $accountModel = AccountModel::find($rechangeModel->accountId);
                            $accountId = DB::connection('db_vr_merchant')->table('account')->where('id',$rechangeModel->accountId)->update([
                                'blance'=>$this->getSumamount($this->getYuanFromFen($data['amount']), $accountModel->blance),'updated_at'=>date('Y-m-d H:i:s')]);
                            if($rechargeId && $recodeId && $accountId){
                                DB::connection('db_vr_merchant')->commit();
                                return 'SUCCESS';
                            }
                            DB::connection('db_vr_merchant')->rollBack();
                            return 'SUCCESS';
                        }
                    }
                }
            } else {
                return 'ERROR';
            }
        } catch (\Exception $exception) {
            Log::info('支付接口回调error:' . json_encode($exception->getMessage()));
            DB::connection('db_vr_merchant')->rollBack();
            return 'ERROR';
        }
    }
    //支付回调订单检查
    public function checkOrder(){
        $merchantOrderNo = Input::get('merchantOrderNo');
        $model = RechargeModel::query()->where('rechargeSerialNo',$merchantOrderNo)->first();
        if(!empty($model)){
            if($model->status == RechangeConst::RECHANGE_ACTION_STATUCT_SUCCESS){//充值成功
                return JsonResponse::success();
            }else if($model->status == RechangeConst::RECHANGE_ACTION_STATUCT_ERROR){//充值失败
                return JsonResponse::error(201);
            }else{
                return JsonResponse::error(202);
            }
        }
        return JsonResponse::error(0);
    }
}