<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 16:41
 */
namespace App\Api\V1\Controllers\Merchant;

use App\Api\V1\Bls\Merchant\Model\MerchantModel;
use App\Api\V1\Bls\Merchant\RechargeBls;
use App\Api\V1\Controllers\BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use JWTAuth,Validator;
use library\Response\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class MerchantController extends BaseController {

    public function __construct() {
        parent::__construct();
    }
    protected $validateRule = [
        'mobile'     => 'required',
        'password'   => 'required',
    ];

    protected $storeValidateRule = [
        'mobile'     => 'required|Numeric|unique:merchant,mobile',
        'password'   => 'required',
        'customertype'=> 'required|Numeric',
    ];
    protected $storeErrorMsg = [
        'mobile.required'              => '登陆账户不能为空',
        'mobile.Numeric'               => '手机号格式不正确',
        'mobile.unique'               => ' 手机号已存在',
        'password.required'            => '密码不能为空',
        'customertype.required'        => '类型不能为空',
        'customertype.Numeric'        => '类型只能为数字',
    ];
    protected $errorMsg = [
        'mobile.required'              => '登陆账户不能为空',
        'password.required'            => '密码不能为空',
    ];
    /**
     *
     * 客户端登陆成功之后保存token失效重新刷新获取token
     * */
    public function login() {
        $validator = Validator::make(Input::all(),$this->validateRule,$this->errorMsg);
        if($validator->fails()){
            return $this->error('0','验证失败',$validator->errors()->toArray());
        }
        $payload = [
            'mobile' => Input::get('mobile'),
            'password' => Input::get('password')
        ];
        try {
            $token = JWTAuth::attempt($payload);
            if (!$token) {
                return JsonResponse::error(0,'用户名或密码错误',[]);
            }
        } catch (JWTException $e) {
            return JsonResponse::error(0,'接口异常');
        }
        return JsonResponse::success(['token'=>$token]);
    }
    /**
     * @author jason
     * @desc 商户注册
     *
     * */
    public function register() {
        $validator = Validator::make(Input::all(),$this->storeValidateRule,$this->storeErrorMsg);
        if($validator->fails()){
            return $this->error('0','验证失败',$validator->errors()->toArray());
        }
        $newUser = [
            'mobile' => Input::get('mobile'),
            'password' => bcrypt(Input::get('password')),
            'customertype' =>Input::get('customertype')
        ];
        $user = MerchantModel::create($newUser);
        $token = JWTAuth::fromUser($user);
        return JsonResponse::success(['access_token'=>$token]);
    }
    /**
     * @author jason
     * @desc 获取用户信息
     * @return object
     * */
    public function getMerchantInfo() {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return JsonResponse::error(0,'商户不存在');
            }
        } catch (TokenExpiredException $e) {
            return JsonResponse::error(0,'error',['token_expired'=>$e->getStatusCode()]);
        } catch (TokenInvalidException $e) {
            return JsonResponse::error(0,'error',['token_invalid'=>$e->getStatusCode()]);
        } catch (JWTException $e) {
            return JsonResponse::error(0,'error',['token_absent'=>$e->getStatusCode()]);
        }
        return JsonResponse::success(['object'=>$user]);
    }

    /**
     * @desc 获取用户充值类型
     * @auth jason
     * @return object
     * */
    public function getMerchantList() {
        $user = JWTAuth::parseToken()->authenticate();
        $searchData = Input::all();
        unset($searchData['token']);  unset($searchData['s']);
        $searchData['accountId'] = $user->account->id;
        $bls = new RechargeBls();
        $recharge = $bls->getRechangeByList($searchData);
        return JsonResponse::success(['object'=>$recharge]);
    }
}