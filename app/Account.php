<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
class Account extends Authenticatable implements AuthenticatableUserContract {

    protected $fillable = ['id','name','mobile','password'];
    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Eloquent model method
    }
    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
