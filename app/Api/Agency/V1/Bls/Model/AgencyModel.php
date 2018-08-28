<?php

namespace App\Api\Agency\V1\Bls\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class AgencyModel extends BaseModel implements AuthenticatableContract,AuthorizableContract,CanResetPasswordContract,JWTSubject  {

    use Authenticatable,CanResetPassword,Authorizable;

    protected $table = 'agent';

    protected $fillable = ['mobile','password','remember_token','status','cipher'];

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }

}