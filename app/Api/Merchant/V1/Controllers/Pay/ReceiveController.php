<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/13
 * Time: 12:01
 */
namespace App\Api\Merchant\V1\Controllers\Pay;
use App\Api\Merchant\V1\Bls\Model\AccountModel;
use App\Api\Merchant\V1\Bls\Model\RechargeModel;
use App\Api\Merchant\V1\Bls\Model\TradeRecodeModel;
use App\Api\Merchant\V1\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use library\Response\JsonResponse;
use library\Service\Contst\Common\RechangeConst;
use library\Service\Contst\Common\StatusConst;
use library\Service\Contst\Pay\PayConst;
use library\Service\Log\BLog;
use library\Service\Pay\Apply;
use library\Service\Pay\Tools\Aes;
use library\Service\Pay\Tools\Rsa;
use Validator,JWTAuth;

class  ReceiveController extends BaseController {

    protected $body;
    protected $blog;
    public function __construct() {
        parent::__construct();
        $this->body = env('MERCHANT_PAY_BODY');
        $this->blog = BLog::get_instance();
    }
    //支付回调接口
    public function receivePay(){
        $responseData = file_get_contents('php://input');
        Log::info('支付接口回调加密前的报文:' . file_get_contents('php://input'));
        if (!empty($responseData)) {
            $responseData = json_decode($responseData, true);
            if (isset($responseData['appId']) && env('LEARNING_PAYAPPID') == $responseData['appId']) {
                $applyPay = new Apply();
                $aesKey = Rsa::privateDecrypt($responseData['encryptKey'], $applyPay::$privateKey);
                $data = Aes::opensslDecrypt($responseData['encryptData'], $aesKey);
                Log::info('支付接口回调解密后的报文:' . $data);
                $data = json_decode($data, true);
                //开启事物
                try {
                    DB::beginTransaction();
                    $rechangeModel = RechargeModel::query()->where('outTradeNo', $data['tradeOrderNo'])->where('status', StatusConst::TRADE_ING)->first();
                    $tradeModel = TradeRecodeModel::query()->where('outTradeNo', $data['tradeOrderNo'])->where('status', StatusConst::TRADE_ING)->get()->first();
                    $status = StatusConst::TRADE_SUCCESS;
                    if ($rechangeModel && $tradeModel) {
                        $rechangeModel->status = $status;
                        $rechangeModel->respCode = $data['respCode'];
                        $rechangeModel->respMsg = $data['respMsg'];
                        $rechangeModel->payTime = $data['payTime'];
                        $rechangeModel->save();
                        $tradeModel->status = $status;
                        $tradeModel->respCode = $data['respCode'];
                        $tradeModel->respMsg = $data['respMsg'];
                        $tradeModel->payTime = $data['payTime'];
                        $tradeModel->save();
                        if ($data['respCode'] == '0000') {
                            $accountModel = AccountModel::find($rechangeModel->accountId);
                            $accountModel->blance = $this->getSumamount($this->getYuanFromFen($data['amount']), $accountModel->blance);
                            $accountModel->save();
                        }
                        DB::commit();
                        return 'SUCCESS';
                    }
                } catch (\Exception $exception) {
                    Log::info('支付接口回调error:' . json_encode($exception->getMessage()));
                    DB::rollBack();
                    return 'ERROR';
                }
            } else {
                return 'ERROR';
            }
        }
        return 'ERROR';
    }

    public function checkOrder(Request $request){
        $merchantOrderNo = $request->get('merchantOrderNo');
        $model = RechargeModel::query()->where('rechargeSerialNo',$merchantOrderNo)->first();
        if(!empty($model)){
            if($model->status == StatusConst::TRADE_SUCCESS){//充值成功
                return JsonResponse::success();
            }else if($model->status == StatusConst::TRADE_FAIL){//充值失败
                return JsonResponse::error(201);
            }else{
                return JsonResponse::error(202);
            }
        }
        return JsonResponse::error(0);
    }
    /**
     * @author jason
     * @desc 下单
     * */
    public function alipayPay() {
        $input = Input::all();
        $validator = Validator::make($input,
            ['amount' => 'required|Numeric', 'rechargeType' => 'required|'.Rule::in(RechangeConst::desc())], ['amount.required' => '充值金额不能为空',
                'amount.Numeric' => '充值金额为整数', 'rechargeType.required'=>'请选择充值方式','rechargeType.in' => '请选择充值方式',]);
        if ($validator->fails()) {
            return JsonResponse::error(0, '验证失败', $validator->errors()->toArray());
        }
        $rechargeType = '';
        $input['amount'] = '0.01';
        if ($input['rechargeType'] == 1) {
            $rechargeType = PayConst::wechatnative;
        } elseif ($input['rechargeType'] == 2) {
            $rechargeType = PayConst::alipay;
        }
        $user = JWTAuth::parseToken()->authenticate();
        $applyPay = new Apply();
        Log::info("商户" . $user->mobile . '-----start---发起的支付' . json_encode(Input::all()));
        $response = $applyPay::unifiedOrder(\helper::getOrderno(), $input['amount'], $this->body, $rechargeType);
        Log::info("商户" .$user->mobile . '-----调用支付接口---发起的支付' . json_encode($response));
        if ($response['code'] && !empty($response['data'])) {
            try {
                DB::beginTransaction();//开启事物
                $payData = \helper::object2array($response['data']);
                //产生预支付订单
                $rechange = new RechargeModel();
                $rechange->merchantId = $user->id;
                $account = AccountModel::query()->where('merchantId', $rechange->merchantId)->first();
                $rechange->accountId = 2;
                $rechange->amount = $this->getYuanFromFen($input['amount']);
                $rechange->rechargeType = $input['rechargeType'];
                $rechange->rechargeSerialNo = $payData['merchantOrderNo'];
                $rechange->outTradeNo = $payData['tradeOrderNo'];
                $rechange->status = StatusConst::TRADE_ING;
                $rechange->ip = \helper::getClientIp();
                $rechange->save();
                //交易记录
                $recode = new TradeRecodeModel();
                $recode->merchantId = $user->id;
                $recode->accountId = $rechange->accountId;
                $recode->recodeType = StatusConst::TRADETUPE_RECHANGE;
                $recode->recodeSerialNo = $payData['merchantOrderNo'];
                $recode->outTradeNo = $payData['tradeOrderNo'];
                $recode->preBlance = $account->blance;
                $recode->blance = $account->blance + $this->getYuanFromFen($input['amount']);
                $recode->preAmount = $account->amount;//变动前可提现金额
                $recode->amount = $account->amount;//变动后可现金额
                $recode->tradeaMount = $this->getYuanFromFen($input['amount']);
                $recode->status = StatusConst::TRADE_ING;
                $recode->ip = \helper::getClientIp();
                $recode->save();
                DB::commit();
                return JsonResponse::success(['qrcodeUrl' => $payData['qrcodeUrl'],'merchantOrderNo'=>$payData['merchantOrderNo']]);
            } catch (\Exception $exception) {
                Log::info("商户异常" . $user->mobile . '-----调用支付接口end---发起的支付' . json_encode($exception->getMessage()));
                DB::rollBack();
                return JsonResponse::error(0, '网络错误！请稍后重试');
            }
        } else {
            return JsonResponse::error(0, '网络错误!请稍后重试');
        }
        Log::info("商户" . $user->mobile . '-----调用支付接口end---发起的支付' . json_encode($response));
    }
}