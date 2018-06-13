<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 11:44
 */
namespace App\Api\V1\Controllers\Auth;

use App\Api\V1\Controllers\BaseController;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class AuthController extends BaseController {
    /**
     * The authentication guard that should be used.
     *
     * @var string
     */
    public function __construct() {
        parent::__construct();
    }
    /**
     * @author jason
     * @desc 刷新token
     *
     * */
    public function refresh(){
        $old_token = JWTAuth::getToken();
        $token = JWTAuth::refresh($old_token);
        JWTAuth::invalidate($old_token);
        return $this->success(['access_token'=>$token,'expires_in'=>JWTAuth::factory()->getTTL()]);
    }
}