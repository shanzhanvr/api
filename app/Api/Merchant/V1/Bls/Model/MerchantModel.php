<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 17:23
 */

namespace App\Api\Merchant\V1\Bls\Model;
use library\Service\Contst\Common\UserTypeConst;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class MerchantModel extends BaseModel implements AuthenticatableContract,AuthorizableContract,CanResetPasswordContract,JWTSubject {

    use Authenticatable,CanResetPassword,Authorizable;
    protected $table = 'merchant';

    protected $fillable =['mobile','password','remember_token','status','agentId'];
    //实现接口下的所有方法
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

    public function account(){
        return $this->hasOne(AccountModel::class,'merchantId');
    }
}