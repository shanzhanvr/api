<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/8/27
 * Time: 14:48
 */

namespace App\Api\Agency\V1\Controllers\Pay;

use App\Api\Agency\V1\Bls\Model\AccountModel;
use App\Api\Agency\V1\Bls\Model\RechangeModel;
use App\Api\Agency\V1\Bls\Model\TradeRecodeModel;
use App\Api\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use library\Service\Contst\Common\RechangeConst;
use library\Service\Log\BLog;
use library\Service\Pay\Apply;
use library\Service\Pay\Tools\Aes;
use library\Service\Pay\Tools\Rsa;

class XubRechangeController extends BaseController {

    protected $blog;
    public function __construct(){
        $this->blog = BLog::get_instance();
    }
    //支付回调接口
    public function receivePay(){
    $responseData = file_get_contents('php://input');
    $this->blog->log('receivepay','支付接口回调解密前的报文:' . $responseData);
    if (!empty($responseData)) {
        $responseData = json_decode($responseData, true);
        if (isset($responseData['appId']) && env('LEARNING_PAYAPPID') == $responseData['appId']) {
            $applyPay = new Apply(env('LEARNING_PAYAPP_AGENCY_NOTIFY'));
            $aesKey = Rsa::privateDecrypt($responseData['encryptKey'], $applyPay::$privateKey);
            $data = Aes::opensslDecrypt($responseData['encryptData'], $aesKey);
            $this->blog->log('receivepay','支付接口回调解密后的报文:' . $data);
            $data = json_decode($data, true);
            DB::connection('db_vr_agency')->beginTransaction();
            //开启事物
            try {
                $rechangeModel = RechangeModel::query()->where('outTradeNo', $data['tradeOrderNo'])->where('status', RechangeConst::RECHANGE_ACTION_STATUCT_ING)->first();
                $tradeModel = TradeRecodeModel::query()->where('outTradeNo', $data['tradeOrderNo'])->where('status', RechangeConst::RECHANGE_ACTION_STATUCT_ING)->first();
                $status = RechangeConst::RECHANGE_ACTION_STATUCT_SUCCESS;
                if ($rechangeModel && $tradeModel) {
                    $rechangeId = DB::connection('db_vr_agency')->table('recharge')->where('rechargeSerialNo',$rechangeModel->rechargeSerialNo)->update(
                        ['status'=>$status,'respCode'=>$data['respCode'],'respMsg'=>$data['respMsg'],'payTime'=>$data['payTime'],'updated_at'=>date('Y-m-d H:i:s')
                        ]);
                    $recodeId = DB::connection('db_vr_agency')->table('recode')->where('recodeSerialNo',$tradeModel->recodeSerialNo)->update(
                        ['status'=>$status,'respCode'=>$data['respCode'],'respMsg'=>$data['respMsg'],'payTime'=>$data['payTime'],'updated_at'=>date('Y-m-d H:i:s')
                        ]);
                    $accountModel = AccountModel::find($rechangeModel->accountId);
                    $accountId = DB::connection('db_vr_agency')->table('account')->where('id',$accountModel->id)->update(
                        ['blance'=>$this->getSumamount($this->getYuanFromFen($data['amount']), $accountModel->blance),'updated_at'=>date('Y-m-d H:i:s')
                        ]);
                    if($rechangeId && $recodeId && $accountId){
                        DB::connection('db_vr_agency')->commit();
                        return 'SUCCESS';
                    }
                    DB::connection('db_vr_agency')->rollBack();
                    return 'ERROR';
                }
            } catch (\Exception $exception) {
                Log::info('支付接口回调error:' . json_encode($exception->getMessage()));
                DB::connection('db_vr_agency')->rollBack();
                return 'ERROR';
            }
        } else {
            return 'ERROR';
        }
    }
    return 'ERROR';
}

}
