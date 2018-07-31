<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
//        config(['jwt.user' => '\App\Api\Ucenter\V1\Bls\Model\User\UserModel']);    //重要用于指定特定model
//        config(['auth.providers.users.model' => \App\Api\Ucenter\V1\Bls\Model\User\UserModel::class]);//重要用于指定特定model！！！！
        return $next($request);
    }
}
