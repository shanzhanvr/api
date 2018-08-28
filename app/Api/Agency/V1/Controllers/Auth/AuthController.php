<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 16:41
 */
namespace App\Api\Agency\V1\Controllers\Auth;


use App\Api\Agency\V1\Bls\Model\AccountModel;
use App\Api\Agency\V1\Bls\Model\AgencyModel;
use App\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use library\Response\JsonResponse;
use library\Service\Contst\Common\StatusConst;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator,JWTAuth;
class AuthController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    protected $validateRule = [
        'mobile' => 'required',
        'password' => 'required',
    ];

    protected $storeValidateRule = [
        'mobile' => 'required|Numeric|unique:merchant,mobile',
        'password' => 'required',
    ];
    protected $storeErrorMsg = [
        'mobile.required' => '登陆账户不能为空',
        'mobile.Numeric' => '手机号格式不正确',
        'mobile.unique' => ' 手机号已存在',
        'password.required' => '密码不能为空',
    ];
    protected $errorMsg = [
        'mobile.required' => '登陆账户不能为空',
        'password.required' => '密码不能为空',
    ];

    /**
     *
     * 客户端登陆成功之后保存token失效重新刷新获取token
     * */
    public function login(Request $request) {
        config(['jwt.user' => '\App\Api\Agency\V1\Bls\Model\AgencyModel']);    //重要用于指定特定model
        config(['auth.providers.users.model' => \App\Api\Agency\V1\Bls\Model\AgencyModel::class]);//重要用于指定特定model！！！！
        $validator = Validator::make(Input::all(), $this->validateRule, $this->errorMsg);
        if ($validator->fails()) {
            return $this->error('0', '验证失败', $validator->errors()->toArray());
        }
        $credentials = [
            'mobile' => Input::get('mobile'),
            'password' => trim(Input::get('password')).trim(Input::get('mobile')),
        ];
        try {
            $token = JWTAuth::attempt($credentials);
            Log::info(json_encode($token));
            if (!$token) {
                return JsonResponse::error(0, '用户名或密码错误', []);
            }
        } catch (JWTException $e) {
            return JsonResponse::error(0, '接口异常');
        }
        return JsonResponse::success(['token' => $token]);
    }

    public function register(){
        config(['jwt.user' => '\App\Api\Agency\V1\Bls\Model\AgencyModel']);    //重要用于指定特定model
        config(['auth.providers.users.model' => \App\Api\Agency\V1\Bls\Model\AgencyModel::class]);//重要用于指定特定model！！！！
        $newUser = [
            'mobile' => Input::get('mobile'),
            'password' => Hash::make(Input::get('password') . Input::get('mobile')),
            'cipher' => Input::get('password'),
            'parentid' => !empty(Input::get('parentid')) ? Input::get('parentid') : 0
        ];
        $model = AgencyModel::query()->where('mobile', Input::get('mobile'))->first();
        if (!empty($model)) {
            return JsonResponse::error(0, '该账户已存在', []);
        }
        $user = AgencyModel::create($newUser);
        AccountModel::create(['agentId' => $user->id, 'blance' =>0, 'amount' => 0, 'status' => StatusConst::ENABLED,'ip' => \helper::getClientIp()]);
        $token = JWTAuth::fromUser($user);
        return JsonResponse::success(['access_token'=>$token]);
    }
    public function getUsers(){
        return JWTAuth::parseToken()->authenticate();
    }
}