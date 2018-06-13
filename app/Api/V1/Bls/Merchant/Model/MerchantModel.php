<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/6/12
 * Time: 17:23
 */
namespace App\Api\V1\Bls\Merchant\Model;
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

    protected $fillable =['mobile','password','remember_token','status'];
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

    public function merchantProfile() {
        return $this->hasOne(MerchantProfileModel::class, 'merchantId','id');
    }
    public function authDatum(){
        if($this->customertype == UserTypeConst::PERSONTYPE){
            return $this->hasOne(PersonauthModel::class, 'merchantId','id');
        }else if($this->customertype == UserTypeConst::COMPANYTYPE){
            return $this->hasOne(CompanyauthModel::class, 'merchantId','id');
        }
    }
}