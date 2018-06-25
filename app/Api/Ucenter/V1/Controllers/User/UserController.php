<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/19
 * Time: 15:03
 */
namespace App\Api\Ucenter\V1\Controllers\User;

use App\Api\Ucenter\V1\Bls\Model\Account\AccountModel;
use App\Api\Ucenter\V1\Bls\Model\User\UserModel;
use App\Api\Ucenter\V1\Controllers\BaseController;
use App\Http\Controllers\Ucenter\H5\User\Tickets\RechangeTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use library\Service\Contst\Common\StatusConst;
use library\Service\Contst\Common\UserTypeConst;
use Validator,Auth;
class UserController extends BaseController {

    use RechangeTraits;

    protected $errorRule = [
        'mobile'=>'required|exists:db_vr_ucenter.ucenter,mobile',
        'password'=>'required'
    ];
    protected $errorMessage = [
        'mobile.required'=>'请输入登录账号',
        'mobile.exists'=>'账号不存在',
        'password.required'=>'密码不能为空',
    ];

    protected $errorStoreRule = [
        'mobile'=>'required|unique:db_vr_ucenter.ucenter,mobile',
        'captcha'=>'required',
        'password'=>'required|between:6,20',
    ];
    protected $errorStoreMessage = [
        'mobile.required'=>'注册手机号不能为空',
        'password.required'=>'密码不能为空',
        'password.password'=>'密码必须在6-20位之间！',
        'captcha.required'=>'短信验证码不能为空',
    ];

    /**
     * @author jaosn
     * @desc 用户登录
     *
     * */
    public function Login(){
        $input = Input::all();
        $validate = Validator::make($input, $this->errorRule, $this->errorMessage);
        if ($validate->fails()) {
            return $this->retError(0, '登录失败', $validate->errors()->toArray());
        }
        $user = UserModel::query()->where('mobile', $input['mobile'])->first();
        if (Hash::check(trim($input['password']), $user->password)) {
            //判断账号是否正常
            if($user->status = StatusConst::ACCOUNT_FROZEN){
                return $this->retError(0, '该账户已冻结');
            }
            if ($input['remember_token']) {
                Auth::login($user, true);
            }
            Auth::login($user);
            return $this->retJson(200, '登录成功');
        } else {
            return $this->retError(0, '密码错误');
        }
    }
    /**
     * @author:jason
     * @desc 注销登录
     *
     * */
    public function logOut(){
        if(Auth::check()){
            Auth::logout();
        }
        return redirect()->route('ucenter.h5.user.login');
    }

    /**
     * @author:jason
     * @desc：注册
     *
     * */
    public function register(Request $request){
        if($request->isMethod('get')) {
            $input = Input::all();
            $validate = Validator::make($input, $this->errorStoreRule, $this->errorStoreMessage);
            if ($validate->fails()) {
                return $this->retError(0, '验证失败', $validate->errors()->toArray());
            }
            //判断短信验证码是否失效
            if (!Cache::store('redis')->has(CacheConst::SEND_MESSAGE_VR_LOGIN . $input['mobile'])) {
                return $this->retError(0, '短信验证码失效');
            }
            $umodel = new UserModel();
            $umodel->mobile = $input['mobile'];
            $umodel->password = Hash::make($input['password']);
            $umodel->ip = \helper::getClientIp();
            $affectId = $umodel->save();
            $account = new AccountModel();
            $account->uid = $affectId;
            $account->blance = 0;
            $account->amount = 0;
            $account->ip = \helper::getClientIp();
            $account->save();
            return $this->success();
        }else{
            return View::make('ucenter.h5.user.register');
        }
    }
    /**
     * @author jason
     * @desc 获取用户充值流水
     *
     * */
    public function getRechange(Request $request){
        $searchData['rechargeType'] = !empty($request->get('rechargeType')) ? $request->get('rechargeType') :'';
        $searchData['rechargeSerialNo'] = !empty($request->get('rechargeSerialNo')) ? $request->get('rechargeSerialNo') :'';
        $searchData['status'] = !empty($request->get('status')) ? $request->get('status') :'';
        $bls = new UserBls();
        $paginate =  $bls->getRechangeList($searchData);

        !empty($searchData['rechargeType']) ? $paginate->appends('rechargeType',$searchData['rechargeType']):'';
        !empty($searchData['rechargeSerialNo']) ? $paginate->appends('rechargeSerialNo',$searchData['rechargeSerialNo']):'';
        !empty($searchData['status']) ? $paginate->appends('rechargeType',$searchData['status']):'';
        return View::make('ucenter.h5.user.rechange',['paginate'=>$paginate,'searchData'=>$searchData]);
    }

    /**
     * @author:jason
     * @desc：获取用户最新10条的收藏
     *
     * */
    public function getCollection(){
        if(!empty( Cache::store('redis')->get(CacheConst::UCENTER_COLLECTION.Auth::user()->id)) || !Cache::store('redis')->has(CacheConst::UCENTER_COLLECTION.Auth::user()->id)){
            return FavoritesModel::query()->where('uid',Auth::user()->id)->orderByRaw('created_at desc ')->take(10);
        }
        return Cache::store('redis')->get(CacheConst::UCENTER_COLLECTION.Auth::user()->id);
    }

    //设置收藏
    public function setCollection(){
        $productId = Input::get('productId');
        $shopId = Input::get('shopId');
        if(!$productId || !$shopId){
            return $this->retError(0,'参数错误');
        }
        $collect = new FavoritesModel();
        $collect->userId = Auth::user()->id;
        $collect->shop = $shopId;
        $collect->targetId = $productId;
        $collect->ip = \helper::getClientIp();
        $collect->save();
        return $this->retJson();
    }



    /**
     * @author jason
     * @desc  发起退款
     *
     * */
    public function refundApply(){
        $orderNo = Input::get('orderNo');
        //判断是否可以发起退款
        $order = OrderModel::query()->where('machBillNo',$orderNo)->first();
        //获取支付
        if(!$orderNo || empty($order)){
            $this->retError(0,'订单不存在');
        }
        //待确认的订单都可以发起退款
        if($order->orderStatus){
           $this->retError(0,'该订单不能退款！');
        }
        //写退款申请
        $refund = new RefundApplyModel();
        $refund->uid = Auth::user()->id;
        $refund->orderId = $order->id;
        $refund->refundOrder = \helper::getOrderno();
        $refund->refundTime = date('Y-m-d H:i:s');
        $refund->refundAmount = $order->amount;
        $refund->cause = Input::get('cause');
        $affectId = $refund->save();
        //写日志
        $refundlog = new RefundLogModel();
        $refundlog->orderId = $order->id;
        $refundlog->refundapplyId = $affectId;
        $refundlog->uid = Auth::user()->id;
        $refundlog->utype = UserTypeConst::USCENTER;
        $refundlog->status = OrderTypeConst::CONFIRM_APPLY_REUND;
        $refundlog->save();
        //更新订单状态
        $order->orderStatus = OrderTypeConst::CONFIRM_APPLY_REUND;
        $order->save();
    }
}