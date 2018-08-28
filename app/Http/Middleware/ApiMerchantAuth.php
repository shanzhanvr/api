<?php

namespace App\Http\Middleware;

use Closure;

class ApiMerchantAuth {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
//    public function handle($request, Closure $next) {
//        config(['jwt.user' => '\App\Api\Merchant\V1\Bls\Model\MerchantModel']);    //重要用于指定特定model
//        config(['auth.providers.users.model' => \App\Api\Merchant\V1\Bls\Model\MerchantModel::class]);//重要用于指定特定model！！！！
//        return $next($request);
//    }
}
